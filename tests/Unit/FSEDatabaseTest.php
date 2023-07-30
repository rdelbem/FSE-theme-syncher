<?php

use Tests\FSEDatabase;

// commons
$fseDatabase = new FSEDatabase();

$expected = [
    (object) [
        'ID' => 1,
        'post_title' => 'FSE is an amazing plugin',
        'post_content' => 'There is no other way to easily synch fse theme changes',
        'post_date' => '2023-07-10 00:00:00',
    ],
    (object) [
        'ID' => 2,
        'post_title' => 'FSE for the win',
        'post_content' => 'Fse is amazing!',
        'post_date' => '2023-07-11 00:00:00',
    ],
];

beforeEach(function () {
    $this->wpdb = \Mockery::mock('overload:wpdb');
    $this->wpdb->prefix = 'wp_';
    $GLOBALS['wpdb'] = $this->wpdb;
});

afterEach(function () {
    \Mockery::close();
});

it('inserts data correctly', function () use($fseDatabase) {
    $data = [
        'column1' => 'value1',
        'column2' => 'value2',
    ];
    $expected = [
        'error' => null,
        'record' => 1
    ];

    $this->wpdb->insert_id = 1;

    $this->wpdb->shouldReceive('insert')
        ->once()
        ->withArgs([$fseDatabase->tableName, $data])
        ->andReturn(true);

    $response = $fseDatabase->insert($data);

    $this->assertEquals($expected, $response);
});

it('gets all data correctly with no particular ordering', function () use ($expected, $fseDatabase) {
    $this->wpdb->shouldReceive('get_results')
        ->once()
        ->andReturn($expected);

    $data = $fseDatabase->getAll();
    $this->assertEquals($expected, $data);
});

it('gets all data correctly with correct ordering', function () use ($expected, $fseDatabase) {
    $this->wpdb->shouldReceive('get_results')
        ->once()
        ->andReturn($expected);

    $dataAsc = $fseDatabase->getAll('ASC');
    $this->assertEquals($expected, $dataAsc);

    $this->wpdb->shouldReceive('get_results')
        ->once()
        ->andReturn(array_reverse($expected));

    $dataDesc = $fseDatabase->getAll('DESC');
    $this->assertEquals(array_reverse($expected), $dataDesc);
});

// TODO: update this test to also cover the $condition
it('gets data by condition correctly', function () use ($expected, $fseDatabase) {
    $conditionValue = ['column1' => 'value1'];
    $this->wpdb->shouldReceive('prepare')
        ->once()
        ->shouldReceive('get_results')
        ->once()
        ->andReturn($expected);

    $fseDatabase->getBy($conditionValue);
    $data = $fseDatabase->getBy($conditionValue);
    $this->assertEquals($expected, $data);
});

it('updates data correctly', function () use($fseDatabase) {
    $data = ['column1' => 'new value1'];
    $conditionValue = ['column1' => 'value1'];

    $this->wpdb->shouldReceive('update')
    ->once()
    ->andReturn(1);

    $response = $fseDatabase->update($data, $conditionValue);

    $this->assertEquals(1, $response);
});

it('should not update if data is empty', function () use($fseDatabase) {
    $data = [];
    $conditionValue = ['column1' => 'value1'];

    $this->wpdb->shouldReceive('update')
    ->once()
    ->andReturn(false);

    $response = $fseDatabase->update($data, $conditionValue);

    $this->assertEquals(false, $response);
});

it('should throw if update doesnt succeed', function () use($fseDatabase) {
    $data = ['column1' => 'new value1'];
    $conditionValue = ['column1' => 'value1'];

    \Mockery::mock('alias:WP_Error');

    $this->wpdb
    ->shouldReceive('update')
    ->once()
    ->andReturn(-1);

    $response = $fseDatabase->update($data, $conditionValue);

    $this->assertInstanceOf('WP_Error', $response);
});

it('should delete data correctly', function () use($fseDatabase) {
    $conditionValue = ['column1' => 'value1'];

    $this->wpdb
    ->shouldReceive('delete')
    ->once()
    ->andReturn(1);

    $response = $fseDatabase->delete($conditionValue);

    $this->assertEquals(1, $response);
});

it('should throw if delete data doesnt succeed', function () use($fseDatabase) {
    $conditionValue = ['column1' => 'value1'];

    \Mockery::mock('alias:WP_Error');

    $this->wpdb
    ->shouldReceive('delete')
    ->once()
    ->andReturn(-1);

    $response = $fseDatabase->delete($conditionValue);

    $this->assertInstanceOf('WP_Error', $response);
});