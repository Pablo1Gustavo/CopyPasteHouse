<?php
declare(strict_types=1);

namespace Tests\Unit;

use App\Casts\CommaSeparatedStringListCast;
use Illuminate\Database\Eloquent\Model;
use PHPUnit\Framework\TestCase;

class CommaSeparatedStringListCastTest extends TestCase
{
    private CommaSeparatedStringListCast $cast;
    private Model $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cast = new CommaSeparatedStringListCast();
        $this->model = $this->createMock(Model::class);
    }

    public function test_converts_comma_separated_string_to_array(): void
    {
        $value = ',php,laravel,javascript,';
        $result = $this->cast->get($this->model, 'test_field', $value, []);

        $this->assertEquals(['php', 'laravel', 'javascript'], $result);
    }

    public function test_returns_null_for_empty_string(): void
    {
        $result = $this->cast->get($this->model, 'test_field', '', []);

        $this->assertNull($result);
    }

    public function test_returns_null_for_null_value(): void
    {
        $result = $this->cast->get($this->model, 'test_field', null, []);

        $this->assertNull($result);
    }

    public function test_handles_single_item_correctly(): void
    {
        $value = ',single-item,';
        $result = $this->cast->get($this->model, 'test_field', $value, []);

        $this->assertEquals(['single-item'], $result);
    }

    public function test_converts_array_to_comma_separated_string(): void
    {
        $value = ['javascript', 'php', 'laravel'];
        $result = $this->cast->set($this->model, 'test_field', $value, []);

        $this->assertEquals(',javascript,laravel,php,', $result);
    }

    public function test_sorts_array_alphabetically_before_conversion(): void
    {
        $value = ['zebra', 'alpha', 'beta'];
        $result = $this->cast->set($this->model, 'test_field', $value, []);

        $this->assertEquals(',alpha,beta,zebra,', $result);
    }

    public function test_converts_items_to_slugs(): void
    {
        $value = ['JavaScript Framework', 'PHP Language', 'Web Development'];
        $result = $this->cast->set($this->model, 'test_field', $value, []);

        $this->assertEquals(',javascript-framework,php-language,web-development,', $result);
    }

    public function test_returns_null_for_empty_array(): void
    {
        $result = $this->cast->set($this->model, 'test_field', [], []);

        $this->assertNull($result);
    }

    public function test_returns_null_for_null_array(): void
    {
        $result = $this->cast->set($this->model, 'test_field', null, []);

        $this->assertNull($result);
    }

    public function test_returns_null_for_non_array_value(): void
    {
        $result = $this->cast->set($this->model, 'test_field', 'not an array', []);

        $this->assertNull($result);
    }

    public function test_handles_single_item_array(): void
    {
        $value = ['single item'];
        $result = $this->cast->set($this->model, 'test_field', $value, []);

        $this->assertEquals(',single-item,', $result);
    }

    public function test_removes_duplicates_when_slugifying(): void
    {
        $value = ['JavaScript', 'javascript', 'JAVASCRIPT'];
        $result = $this->cast->set($this->model, 'test_field', $value, []);

        $this->assertEquals(',javascript,', $result);
    }


    public function test_can_do_full_round_trip_conversion(): void
    {
        $originalArray = ['Web Development', 'PHP', 'Laravel Framework'];

        $stringValue = $this->cast->set($this->model, 'test_field', $originalArray, []);
        $this->assertEquals(',laravel-framework,php,web-development,', $stringValue);

        $resultArray = $this->cast->get($this->model, 'test_field', $stringValue, []);
        $this->assertEquals(['laravel-framework', 'php', 'web-development'], $resultArray);
    }

    public function test_handles_edge_case_with_commas_only(): void
    {
        $value = ',,';
        $result = $this->cast->get($this->model, 'test_field', $value, []);

        $this->assertEquals([''], $result);
    }

    public function test_handles_array_with_empty_strings(): void
    {
        $value = ['', 'valid-item', ''];
        $result = $this->cast->set($this->model, 'test_field', $value, []);

        $this->assertEquals(',valid-item,', $result);
    }
}
