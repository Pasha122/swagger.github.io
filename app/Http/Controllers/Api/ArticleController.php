<?php

namespace App\Http\Controllers\Api;
 
use Exception;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\BaseController;

class ArticleController extends Controller
{
     /**
     * @OA\Get(
     *    path="/articles",
     *    operationId="index",
     *    tags={"Articles"},
     *    summary="Get list articles",
     *    description="Get list articles",
     *    @OA\Parameter(name="limit", in="query", description="limit", required=false,
     *        @OA\Schema(type="integer")
     *    ),
     *    @OA\Parameter(name="page", in="query", description="the page number", required=false,
     *        @OA\Schema(type="integer")
     *    ),
     *     @OA\Response(
     *          response=200, description="Success",
     *          @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example="200"),
     *             @OA\Property(property="data",type="object")
     *          )
     *       )
     *  )
     */
    public function index(Request $request)
    {
        try{
            $limit = $request->limit ?: 15;
            $order = $request->order == 'asc' ? 'asc' : 'desc';
 
            $articles = Article::orderBy('updated_at', $order)
                ->select('id', 'title', 'content', 'status')
                ->where('status', 'Published')
                ->paginate($limit);
 
            return response()->json(['status' => 200, 'data' => $articles]);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'message' => $e->getMessage()]);
        }
    }
 
    /**
     * @OA\Post(
     *      path="/articles",
     *      operationId="store",
     *      tags={"Articles"},
     *      summary="Store article in DB",
     *      description="Store article in DB",
     *      @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *            required={"title", "content", "status"},
     *            @OA\Property(property="title", type="string", format="string", example="Test Article"),
     *            @OA\Property(property="content", type="string", format="string", example="Description"),
     *            @OA\Property(property="status", type="string", format="string", example="Published"),
     *         ),
     *      ),
     *     @OA\Response(
     *          response=200, description="Success",
     *          @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=""),
     *             @OA\Property(property="data",type="object")
     *          )
     *       )
     *  )
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
 
            $article = Article::create($request->only('title', 'content', 'status'));
            DB::commit();
 
            return response()->json(['status' => 201, 'data' => $article]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 400, 'message' => $e->getMessage()]);
        }
    }
 
    /**
     * @OA\Get(
     *    path="/articles/{id}",
     *    operationId="show",
     *    tags={"Articles"},
     *    summary="Get Article",
     *    description="Get Article",
     *    @OA\Parameter(name="id", in="path", description="Id of Article", required=true,
     *        @OA\Schema(type="integer")
     *    ),
     *     @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *          @OA\Property(property="status_code", type="integer", example="200"),
     *          @OA\Property(property="data",type="object")
     *           ),
     *        )
     *       )
     *  )
     */
    public function show(Article $article)
    {
        try {
            return response()->json(['status' => 200, 'data' => $article]);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'message' => $e->getMessage()]);
        }
    }
 
    /**
     * @OA\Put(
     *     path="/articles/{id}",
     *     operationId="update",
     *     tags={"Articles"},
     *     summary="Update article in DB",
     *     description="Update article in DB",
     *     @OA\Parameter(name="id", in="path", description="Id of Article", required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *        required=true,
     *        @OA\JsonContent(
     *           required={"title", "content", "status"},
     *           @OA\Property(property="title", type="string", format="string", example="Test Article Title"),
     *           @OA\Property(property="content", type="string", format="string", example="Description"),
     *           @OA\Property(property="status", type="string", format="string", example="Published"),
     *        ),
     *     ),
     *     @OA\Response(
     *          response=200, description="Success",
     *          @OA\JsonContent(
     *             @OA\Property(property="status_code", type="integer", example="200"),
     *             @OA\Property(property="data",type="object")
     *          )
     *       )
     *  )
     */
    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();
 
            $article = Article::updateOrCreate(['id' => $id], $request->only('title', 'content', 'status'));
            DB::commit();
            return response()->json(['status' => 200, 'data' => $article]);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json(['status' => 400, 'message' => $e->getMessage()]);
        }
    }
 
    /**
     * @OA\Delete(
     *    path="/articles/{id}",
     *    operationId="destroy",
     *    tags={"Articles"},
     *    summary="Delete Article",
     *    description="Delete Article",
     *    @OA\Parameter(name="id", in="path", description="Id of Article", required=true,
     *        @OA\Schema(type="integer")
     *    ),
     *    @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *         @OA\Property(property="status_code", type="integer", example="200"),
     *         @OA\Property(property="data",type="object")
     *          ),
     *       )
     *      )
     *  )
     */
    public function destroy($id)
    {
        try {
            Article::find($id)->delete();
 
            return response()->json(['status' => 200, 'data' => []]);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'message' => $e->getMessage()]);
        }
    }
}
