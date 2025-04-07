<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use Mockery;
use App\Models\Workshop;
use App\Models\WorkshopAd;
use App\Repositories\WorkshopAdRepository;
use App\Services\WorkshopAdService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class WorkshopAdServiceTest extends TestCase
{
    use RefreshDatabase;

    private WorkshopAdService $service;
    private $repositoryMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repositoryMock = Mockery::mock(WorkshopAdRepository::class);
        $this->service = new WorkshopAdService($this->repositoryMock);
    }

/** @test */
public function it_creates_a_workshop_ad()
{

    $workshop = Workshop::factory()->create();
    
    $data = [
        'title'       => 'Test Ad',
        'description' => 'Test Description',
        'price'       => 100,
    ];

    $createdAd = new WorkshopAd(array_merge($data, ['workshop_id' => $workshop->id]));

    $this->repositoryMock
        ->shouldReceive('create')
        ->once()
        ->with(Mockery::on(function ($arg) use ($data, $workshop) {
            return $arg['title'] === $data['title']
                && $arg['description'] === $data['description']
                && $arg['price'] === $data['price']
                && $arg['workshop_id'] === $workshop->id;
        }))
        ->andReturn($createdAd);

 
    $response = $this->service->createWorkshopAd($data, $workshop);

 
    $this->assertInstanceOf(\Illuminate\Http\JsonResponse::class, $response);


    $dataResponse = $response->getData(true);

    $this->assertEquals($data['title'], $dataResponse['title']);
    $this->assertEquals($data['description'], $dataResponse['description']);
    $this->assertEquals($data['price'], $dataResponse['price']);
    $this->assertEquals($workshop->id, $dataResponse['workshop_id']);
}

}
