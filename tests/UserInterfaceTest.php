<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class ExampleTest extends TestCase
{
    public function testGetUserList()
    {
        $this->get('/v1/get_user_list?page_size=1');
        $result = json_decode($this->response->getContent(), true);

        $this->assertEquals($result['code'], 0);
        $this->assertLessThanOrEqual(count($result['data']), 1);
    }

    public function testCreateUser()
    {
        // 新增用户
        $data = [
            'email' => 'test_email'.uniqid().'@a.com',
            'password' => 'test_create_password'
        ];
        $this->json('POST', '/v1/create_user', $data);
        $result = json_decode($this->response->getContent(), true);

        $this->assertEquals(0, $result['code']);
        $this->assertGreaterThanOrEqual(1, $result['data']['id']);

        // 测试是否登录成功
        $this->json('POST', '/v1/login', $data);
        $result = json_decode($this->response->getContent(), true);

        $this->assertEquals(0, $result['code']);
        $this->assertArrayHasKey('token', $result['data']);
    }

    public function testCreateUserWithoutParam()
    {
        $this->json('POST', '/v1/create_user');
        $result = json_decode($this->response->getContent(), true);

        $this->assertEquals(500000, $result['code']);
    }

    public function testCreateUserWithoutPassword()
    {
        $data = [
            'email' => 'test_email'.uniqid().'@a.com',
        ];
        $this->json('POST', '/v1/create_user', $data);
        $result = json_decode($this->response->getContent(), true);

        $this->assertEquals(500000, $result['code']);
    }

    public function testUpdateUser()
    {
        // 更新用户
        $data = [
            'id' => 1,
            'email' => 'test_email'.uniqid().'@a.com',
            'password' => 'test_update_password'
        ];

        $this->json('POST', '/v1/update_user', $data);
        $result = json_decode($this->response->getContent(), true);

        $this->assertEquals(0, $result['code']);
        $this->assertEquals(1, $result['data']['id']);


        // 测试是否登录成功
        $this->json('POST', '/v1/login', $data);
        $result = json_decode($this->response->getContent(), true);

        $this->assertEquals(0, $result['code']);
        $this->assertArrayHasKey('token', $result['data']);
    }

    public function testUpdateUserWithoutParam()
    {

        $this->json('POST', '/v1/update_user');
        $result = json_decode($this->response->getContent(), true);

        // 这段逻辑，由于缺少 id 表现为新增用户，因此返回”创建失败，用户信息有误“
        $this->assertEquals(500003, $result['code']);
    }

    public function testUpdateUserWithoutId()
    {
        $data = [
            'email' => 'test_email'.uniqid().'@a.com',
            'password' => 'test_update_password'
        ];
        $this->json('POST', '/v1/update_user', $data);
        $result = json_decode($this->response->getContent(), true);

        $this->assertEquals(0, $result['code']);
        $this->assertGreaterThanOrEqual(1, $result['data']['id']);
    }

    public function testUpdateUserWithoutPassword()
    {
        // 更新为测试数据
        $data = [
            'id' => 1,
            'email' => 'test_email'.uniqid().'@a.com',
            'password' => 'test_update_password'
        ];
        $this->json('POST', '/v1/update_user', $data);
        $result = json_decode($this->response->getContent(), true);

        $this->assertEquals(0, $result['code']);

        // 移除密码
        $data_without_password = $data;
        unset($data_without_password['password']);
        $this->json('POST', '/v1/update_user', $data_without_password);

        $result = json_decode($this->response->getContent(), true);
        $this->assertEquals(0, $result['code']);

        // 测试是否登录成功
        $this->json('POST', '/v1/login', $data);
        $result = json_decode($this->response->getContent(), true);

        $this->assertEquals(0, $result['code']);
        $this->assertArrayHasKey('token', $result['data']);
    }

    public function testDeleteUser()
    {
        // 新增用户
        $data = [
            'email' => 'test_email'.uniqid().'@a.com',
            'password' => 'test_create_password'
        ];
        $this->json('POST', '/v1/create_user', $data);
        $result = json_decode($this->response->getContent(), true);

        $this->assertEquals(0, $result['code']);
        $data_for_delete = $result['data'];

        // 测试是否登录成功
        $this->json('POST', '/v1/login', $data);
        $result = json_decode($this->response->getContent(), true);

        $this->assertEquals(0, $result['code']);
        $this->assertArrayHasKey('token', $result['data']);


        // 删除用户
        $this->json('POST', '/v1/delete_user', $data_for_delete);
        $result = json_decode($this->response->getContent(), true);

        $this->assertEquals(0, $result['code']);
        $this->assertEquals($data_for_delete['id'], $result['data']['id']);


        // 测试是否登录成功
        $this->json('POST', '/v1/login', $data);
        $result = json_decode($this->response->getContent(), true);

        $this->assertEquals(500001, $result['code']);
    }
}
