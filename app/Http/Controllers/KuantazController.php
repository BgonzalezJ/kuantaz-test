<?php

namespace App\Http\Controllers;

use App\Services\KuantazEndpointsService;
use Illuminate\Support\Collection;

/**
 * @OA\Info(
 *     title="Kuantaz documentación",
 *     version="1.0.0",
 *     description="Documentación del endpoint de beneficios"
 * )
 * @OA\Tag(
 *     name="Beneficios",
 *     description="endpoints relacionados con beneficios"
 * )
 */

class KuantazController extends Controller
{
    protected $kuantasEndpoints;

    public function __construct(KuantazEndpointsService $kuantasEndpoints) {
        $this->kuantasEndpoints = $kuantasEndpoints;
    }

    /**
     * @OA\Get(
     *     path="/benefits",
     *     summary="Obtener beneficios",
     *     tags={"Beneficios"},
     *     @OA\Response(
     *         response=200,
     *         description="Respuesta con lista de beneficios",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="year", type="integer", example=2024),
     *                     @OA\Property(property="num", type="integer", example=5),
     *                     @OA\Property(property="total", type="integer", example=20000),
     *                     @OA\Property(
     *                         property="beneficios",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="id_programa", type="integer", example=100),
     *                             @OA\Property(property="monto", type="integer", example=20000),
     *                             @OA\Property(property="fecha_recepcion", type="date", example="10/01/2024"),
     *                             @OA\Property(property="fecha", type="date", example="2024-01-10"),
     *                             @OA\Property(property="ano", type="integer", example=2024),
     *                             @OA\Property(property="view", type="integer", example=true),
     *                             @OA\Property(
     *                                property="ficha",
     *                                type="object",
     *                                @OA\Property(property="id", type="integer", example=199),
     *                                @OA\Property(property="nombre", type="string", example="Subsidio"),
     *                                @OA\Property(property="id_programa", type="integer", example=100),
     *                                @OA\Property(property="url", type="string", example="subsidio_familiar"),
     *                                @OA\Property(property="categoria", type="string", example="bono"),
     *                                @OA\Property(property="descripcion", type="string", example="Beneficio económico"),
     *                             )
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function benefits() {
        $benefits = $this->kuantasEndpoints->getBenefits();
        $filters = $this->kuantasEndpoints->getFilters();
        $files = $this->kuantasEndpoints->getFiles();

        if (empty($benefits) || empty($filters)) {
            return response()->json(['success' => false, 'code' => 200, 'data' => []]);
        }

        $collection = new Collection();

        foreach ($benefits as $benefit) {
            $year = date('Y', strtotime($benefit['fecha']));

            if ($collection->where('year', $year)->isEmpty()) {
                $collection->add(['year' => $year, 'num' => 0, 'total' => 0, 'beneficios' => new Collection()]);
            }

            $programId = $benefit['id_programa'];

            $filter = array_values(array_filter($filters, function ($el) use($programId) {
                return $el['id_programa'] == $programId;
            }));

            $benefitToAdd = [];
            $monto = $benefit['monto'];

            if (!empty($filter)) {
                $filter = $filter[0];
                if ($monto >= $filter['min'] && $monto <= $filter['max']) {
                    $fileId = $filter['ficha_id'];
                    $file = array_values(array_filter($files, function ($el) use($fileId) {
                        return $el['id'] == $fileId;
                    }));

                    if (!empty($file)) {
                        $file = $file[0];
                        $benefitToAdd = array_merge($benefit, ['ano' => $year, 'view' => true, 'ficha' => $file]);
                    }
                }
            }

            $collection = $collection->map(function ($item) use ($year, $monto, $benefitToAdd) {
                if ($item['year'] == $year) {
                    $item['num'] += 1;
                    $item['total'] += $monto;
                    if (!empty($benefitToAdd)) {
                        $item['beneficios']->add($benefitToAdd);
                    }
                }
                return $item;
            });
        }

        $collection = $collection->map(function ($item) {
            $item['beneficios'] = $item['beneficios']->sortBy('fecha')->values();
            return $item;
        });

        $response = [
            'success' => true,
            'code' => 200,
            'data' => $collection->sortByDesc('year')->values()
        ];

        return response()->json($response);
    }
}
