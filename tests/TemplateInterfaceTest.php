<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class TemplateInterfaceTest extends TestCase
{

    public function testGetTemplateList()
    {
        $this->get('/v1/get_template_list?page_size=1');
        $result = json_decode($this->response->getContent(), true);

        $this->assertEquals($result['code'], 0);
        $this->assertLessThanOrEqual(count($result['data']), 1);
    }

    public function testCreateTemplate()
    {
        $data = [
            'image' => 'test_create_url',
            'description' => 'test_create_description'
        ];
        $this->json('POST', '/v1/create_template', $data);
        $result = json_decode($this->response->getContent(), true);

        $this->assertEquals(0, $result['code']);
        $this->assertGreaterThanOrEqual(1, $result['data']['id']);
    }

    public function testCreateTemplateWithoutParam()
    {
        $this->json('POST', '/v1/create_template');
        $result = json_decode($this->response->getContent(), true);

        $this->assertEquals(500000, $result['code']);
    }

    public function testCreateTemplateWithoutDescription()
    {
        $data = [
            'image' => 'test_create_url'
        ];
        $this->json('POST', '/v1/create_template', $data);
        $result = json_decode($this->response->getContent(), true);

        $this->assertEquals(500000, $result['code']);
    }

    public function testUpdateTemplate()
    {
        $data = [
            'id' => 1,
            'image' => 'test_update_url',
            'description' => 'test_update_description'
        ];
        $this->json('POST', '/v1/update_template', $data);
        $result = json_decode($this->response->getContent(), true);

        $this->assertEquals(0, $result['code']);
        $this->assertEquals(1, $result['data']['id']);
    }

    public function testUpdateTemplateWithoutParam()
    {
        $this->json('POST', '/v1/update_template');
        $result = json_decode($this->response->getContent(), true);

        // 这段逻辑，由于缺少 id 表现为新增模板，因此返回”创建失败，模板信息有误“
        $this->assertEquals(500005, $result['code']);
    }

    public function testUpdateTemplateWithoutId()
    {
        $data = [
            'image' => 'test_update_url',
            'description' => 'test_update_description'
        ];
        $this->json('POST', '/v1/update_template', $data);
        $result = json_decode($this->response->getContent(), true);

        $this->assertEquals(0, $result['code']);
        $this->assertGreaterThanOrEqual(1, $result['data']['id']);
    }

    public function testUpdateTemplateWithoutIdAndImage()
    {
        $data = [
            'description' => 'test_update_description'
        ];
        $this->json('POST', '/v1/update_template', $data);
        $result = json_decode($this->response->getContent(), true);

        $this->assertEquals(500005, $result['code']);
    }

    public function testUpdateTemplateWithoutDescription()
    {
        $data = [
            'id' => 1,
            'image' => 'test_update_image'
        ];
        $this->json('POST', '/v1/update_template', $data);
        $result = json_decode($this->response->getContent(), true);

        $this->assertEquals(0, $result['code']);
        $this->assertGreaterThanOrEqual(1, $result['data']['id']);
    }
}
