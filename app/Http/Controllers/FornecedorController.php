<?php

namespace App\Http\Controllers;

use App\Models\Fornecedor;
use Illuminate\Http\Request;
use App\Rules\ValidCNPJ;
use App\Rules\ValidCPF;
use App\Services\BrasilAPIService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;

class FornecedorController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Fornecedor::query();
            
            if ($request->has('tipo')) {
                $query->where('tipo', $request->tipo);
            }
            
            if ($request->has('documento')) {
                $query->where('documento', preg_replace('/[^0-9]/', '', $request->documento));
            }
            
            if ($request->has('nome')) {
                $query->where('nome_razao_social', 'like', '%'.$request->nome.'%');
            }
            
            return $query->orderBy('nome_razao_social')
                ->paginate($request->get('per_page', 15));
                        
        } catch (\Exception $e) {
            Log::error('Erro ao listar fornecedores: ' . $e->getMessage());
            return response()->json([
                'message' => 'Erro ao listar fornecedores',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request, BrasilAPIService $brasilApi)
    {
        try {
            $validator = Validator::make($request->all(), [
                'tipo' => 'required|in:PF,PJ',
                'documento' => [
                    'required',
                    'string',
                    $request->tipo == 'PJ' ? new ValidCNPJ : new ValidCPF,
                    'unique:fornecedores,documento'
                ],
                'nome_razao_social' => 'required|string|max:255',
                'nome_fantasia' => 'nullable|string|max:255',
                'telefone' => 'required|string|max:20',
                'email' => 'required|email|unique:fornecedores,email',
                'cep' => 'required|string|max:9',
                'logradouro' => 'required|string|max:255',
                'numero' => 'required|string|max:10',
                'complemento' => 'nullable|string|max:255',
                'bairro' => 'required|string|max:255',
                'cidade' => 'required|string|max:255',
                'uf' => 'required|string|size:2',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $validated = $validator->validated();
            $validated['documento'] = preg_replace('/[^0-9]/', '', $validated['documento']);
            $validated['cep'] = preg_replace('/[^0-9]/', '', $validated['cep']);

            // Consulta BrasilAPI para PJ
            if ($request->tipo == 'PJ') {
                try {
                    $dadosApi = $brasilApi->consultarCNPJ($validated['documento']);
                    
                    if ($dadosApi) {
                        $validated = array_merge($validated, [
                            'nome_razao_social' => $dadosApi['razao_social'] ?? $validated['nome_razao_social'],
                            'nome_fantasia' => $dadosApi['nome_fantasia'] ?? $validated['nome_fantasia'],
                            'logradouro' => $dadosApi['logradouro'] ?? $validated['logradouro'],
                            'bairro' => $dadosApi['bairro'] ?? $validated['bairro'],
                            'cidade' => $dadosApi['cidade'] ?? $validated['cidade'],
                            'uf' => $dadosApi['uf'] ?? $validated['uf'],
                            'cep' => $dadosApi['cep'] ?? $validated['cep'],
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::warning('Erro ao consultar BrasilAPI: ' . $e->getMessage());
                }
            }

            $fornecedor = Fornecedor::create($validated);
            
            return response()->json($fornecedor, 201);
            
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Erro de validação',
                'errors' => $e->errors()
            ], 422);
            
        } catch (QueryException $e) {
            if ($e->errorInfo[1] == 1062) {
                return response()->json([
                    'message' => 'Documento ou e-mail já cadastrado',
                    'errors' => [
                        'documento' => ['Este documento já está em uso'],
                        'email' => ['Este e-mail já está em uso']
                    ]
                ], 422);
            }
            
            Log::error('Erro de banco de dados: ' . $e->getMessage());
            return response()->json([
                'message' => 'Erro ao criar fornecedor',
                'error' => $e->getMessage()
            ], 500);
            
        } catch (\Exception $e) {
            Log::error('Erro ao criar fornecedor: ' . $e->getMessage());
            return response()->json([
                'message' => 'Erro ao criar fornecedor',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            return response()->json(Fornecedor::findOrFail($id));
        } catch (\Exception $e) {
            Log::error('Erro ao buscar fornecedor: ' . $e->getMessage());
            return response()->json([
                'message' => 'Fornecedor não encontrado',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $fornecedor = Fornecedor::findOrFail($id);
            
            $validator = Validator::make($request->all(), [
                'tipo' => 'sometimes|in:PF,PJ',
                'documento' => [
                    'sometimes',
                    'string',
                    $request->has('tipo') && $request->tipo == 'PJ' ? new ValidCNPJ : new ValidCPF,
                    'unique:fornecedores,documento,'.$fornecedor->id
                ],
                'nome_razao_social' => 'sometimes|string|max:255',
                'nome_fantasia' => 'nullable|string|max:255',
                'telefone' => 'sometimes|string|max:20',
                'email' => 'sometimes|email|unique:fornecedores,email,'.$fornecedor->id,
                'cep' => 'sometimes|string|max:9',
                'logradouro' => 'sometimes|string|max:255',
                'numero' => 'sometimes|string|max:10',
                'complemento' => 'nullable|string|max:255',
                'bairro' => 'sometimes|string|max:255',
                'cidade' => 'sometimes|string|max:255',
                'uf' => 'sometimes|string|size:2',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $validated = $validator->validated();
            
            if (isset($validated['documento'])) {
                $validated['documento'] = preg_replace('/[^0-9]/', '', $validated['documento']);
            }
            if (isset($validated['cep'])) {
                $validated['cep'] = preg_replace('/[^0-9]/', '', $validated['cep']);
            }

            $fornecedor->update($validated);
            
            return response()->json($fornecedor);
            
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Erro de validação',
                'errors' => $e->errors()
            ], 422);
            
        } catch (QueryException $e) {
            if ($e->errorInfo[1] == 1062) {
                return response()->json([
                    'message' => 'Documento ou e-mail já cadastrado',
                    'errors' => [
                        'documento' => ['Este documento já está em uso'],
                        'email' => ['Este e-mail já está em uso']
                    ]
                ], 422);
            }
            
            Log::error('Erro de banco de dados ao atualizar: ' . $e->getMessage());
            return response()->json([
                'message' => 'Erro ao atualizar fornecedor',
                'error' => $e->getMessage()
            ], 500);
            
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar fornecedor: ' . $e->getMessage());
            return response()->json([
                'message' => 'Erro ao atualizar fornecedor',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $fornecedor = Fornecedor::findOrFail($id);
            $fornecedor->delete();
            
            return response()->json(null, 204);
            
        } catch (\Exception $e) {
            Log::error('Erro ao excluir fornecedor: ' . $e->getMessage());
            return response()->json([
                'message' => 'Erro ao excluir fornecedor',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}