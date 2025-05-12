<?php

namespace Tests\Unit;

use App\Services\KuantazEndpointsService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class KuantazEndpointsServiceTest extends TestCase {
    public function test() {
        Http::fake([
            config('services.kuantaz.benefits_endpoint') => Http::response($this->benefitsEndpointsResponse(), 200),
            config('services.kuantaz.filters_endpoint') => Http::response($this->filtersEndpointsResponse(), 200),
            config('services.kuantaz.files_endpoint') => Http::response($this->filesEndpointsResponse(), 200),
        ]);

        $service = new KuantazEndpointsService();
        $resultBenefits = $service->getBenefits();
        $resultFilters = $service->getFilters();
        $resultFiles = $service->getFiles();

        $this->assertEquals($this->benefitsEndpointsResponse()['data'], $resultBenefits);
        $this->assertEquals($this->filtersEndpointsResponse()['data'], $resultFilters);
        $this->assertEquals($this->filesEndpointsResponse()['data'], $resultFiles);
    }

    private function benefitsEndpointsResponse() {
        return [
            'code' => 200,
            'success' => true,
            'data' => [
                [
                    "id_programa" => 147,
                    "monto" => 40656,
                    "fecha_recepcion" => "09/11/2023",
                    "fecha" => "2023-11-09"
                ],
                [
                    "id_programa" => 147,
                    "monto" => 60000,
                    "fecha_recepcion" => "10/10/2023",
                    "fecha" => "2023-10-10"
                ],
                [
                    "id_programa" => 130,
                    "monto" => 40656,
                    "fecha_recepcion" => "08/09/2023",
                    "fecha" => "2023-09-08"
                ],
            ]
            ];
    }

    private function filtersEndpointsResponse() {
        return [
            'code' => 200,
            'success' => true,
            'data' => [
                [
                    "id_programa" => 147,
                    "tramite" => "Emprende ",
                    "min" => 0,
                    "max" => 50000,
                    "ficha_id" => 922
                ],
                [
                    "id_programa" => 146,
                    "tramite" => "Crece",
                    "min" => 0,
                    "max" => 30000,
                    "ficha_id" => 903
                ],
                [
                    "id_programa" => 130,
                    "tramite" => "Subsidio Único Familiar",
                    "min" => 5000,
                    "max" => 180000,
                    "ficha_id" => 2042
                ],
            ]
        ];
    }

    private function filesEndpointsResponse() {
        return [
            'code' => 200,
            'success' => true,
            'data' => [
                [
                    "id" => 903,
                    "nombre" => "Crece",
                    "id_programa" => 146,
                    "url" => "crece",
                    "categoria" => "trabajo",
                    "descripcion" => "Subsidio para implementar plan de trabajo en empresas"
                ],
                [
                    "id" => 922,
                    "nombre" => "Emprende",
                    "id_programa" => 147,
                    "url" => "emprende",
                    "categoria" => "trabajo",
                    "descripcion" => "Fondos concursables para nuevos negocios"
                ],
                [
                    "id" => 2042,
                    "nombre" => "Subsidio Familiar (SUF)",
                    "id_programa" => 130,
                    "url" => "subsidio_familiar_suf",
                    "categoria" => "bonos",
                    "descripcion" => "Beneficio económico mensual entregado a madres, padres o tutores que no cuentan con previsión social."
                ]
            ]
        ];
    }
}
