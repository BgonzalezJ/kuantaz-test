<?php
namespace Tests\Feature;

use Tests\TestCase;
use App\Services\KuantazEndpointsService;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApiControllerTest extends TestCase
{
    public function test() {
        $mock = $this->createMock(KuantazEndpointsService::class);

        $mock->method('getBenefits')->willReturn($this->benefitsEndpointsResponse());
        $mock->method('getFilters')->willReturn($this->filtersEndpointsResponse());
        $mock->method('getFiles')->willReturn($this->filesEndpointsResponse());

        // Registrar el mock en el contenedor
        $this->app->instance(KuantazEndpointsService::class, $mock);

        // Hacer la petición
        $response = $this->getJson('/benefits');

        // Afirmaciones
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'code',
            'data'
        ]);
    }

    private function benefitsEndpointsResponse() {
        return [
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
        ];
    }

    private function filtersEndpointsResponse() {
        return [
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
        ];
    }

    private function filesEndpointsResponse() {
        return [
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
        ];
    }
}
