<?php

declare(strict_types=1);

use App\Actions\BaseAction;
use App\Actions\Contracts\ActionInterface;
use Illuminate\Support\Facades\Log;

/**
 * Test implementation of BaseAction for testing purposes
 */
class TestAction extends BaseAction
{
    public function handle(mixed $input = null): mixed
    {
        if ($input === 'throw') {
            throw new \RuntimeException('Test exception');
        }
        
        if ($input === 'null-required') {
            $this->validateInputNotNull(null, 'Test input cannot be null');
        }
        
        if ($input === 'type-validation') {
            $this->validateInputType($input, 'integer', 'Test input must be integer');
        }
        
        if ($input === 'array-validation') {
            $this->validateArrayInput($input, ['required_key'], 'Test array validation');
        }
        
        return $input ?? 'default-result';
    }
}

describe('BaseAction', function () {
    beforeEach(function () {
        $this->action = new TestAction();
    });

    it('implements ActionInterface', function () {
        expect($this->action)->toBeInstanceOf(ActionInterface::class);
    });

    it('can be extended by concrete action classes', function () {
        expect($this->action)->toBeInstanceOf(BaseAction::class);
        expect(method_exists($this->action, 'handle'))->toBeTrue();
        expect(method_exists($this->action, 'execute'))->toBeTrue();
    });

    describe('handle method', function () {
        it('returns input when provided', function () {
            $input = 'test-input';
            $result = $this->action->handle($input);
            
            expect($result)->toBe($input);
        });

        it('returns default result when no input provided', function () {
            $result = $this->action->handle();
            
            expect($result)->toBe('default-result');
        });

        it('can handle null input', function () {
            $result = $this->action->handle(null);
            
            expect($result)->toBe('default-result');
        });
    });

    describe('execute method', function () {
        it('calls handle method and returns result', function () {
            $input = 'test-input';
            $result = $this->action->execute($input);
            
            expect($result)->toBe($input);
        });

        it('logs action start and success in debug mode', function () {
            config(['app.debug' => true]);
            Log::shouldReceive('debug')
                ->twice()
                ->withArgs(function ($message, $context) {
                    return str_contains($message, 'TestAction') && 
                           isset($context['action']) && 
                           isset($context['timestamp']);
                });

            $this->action->execute('test-input');
        });

        it('does not log when debug mode is disabled', function () {
            config(['app.debug' => false]);
            Log::shouldReceive('debug')->never();

            $this->action->execute('test-input');
        });

        it('logs errors and re-throws exceptions', function () {
            config(['app.debug' => true]);
            
            Log::shouldReceive('debug')->once(); // Start log
            Log::shouldReceive('error')
                ->once()
                ->withArgs(function ($message, $context) {
                    return str_contains($message, 'Action failed: TestAction') &&
                           isset($context['exception']) &&
                           isset($context['exception_class']) &&
                           $context['exception'] === 'Test exception';
                });

            expect(fn() => $this->action->execute('throw'))
                ->toThrow(\RuntimeException::class, 'Test exception');
        });
    });

    describe('getActionName method', function () {
        it('returns class name without namespace', function () {
            $reflection = new \ReflectionClass($this->action);
            $method = $reflection->getMethod('getActionName');
            $method->setAccessible(true);
            
            $actionName = $method->invoke($this->action);
            
            expect($actionName)->toBe('TestAction');
        });
    });

    describe('validation methods', function () {
        describe('validateInputNotNull', function () {
            it('throws exception when input is null', function () {
                expect(fn() => $this->action->handle('null-required'))
                    ->toThrow(\InvalidArgumentException::class, 'Test input cannot be null');
            });

            it('does not throw when input is not null', function () {
                $result = $this->action->handle('valid-input');
                expect($result)->toBe('valid-input');
            });
        });

        describe('validateInputType', function () {
            it('throws exception when input type does not match', function () {
                expect(fn() => $this->action->handle('type-validation'))
                    ->toThrow(\InvalidArgumentException::class, 'Test input must be integer');
            });
        });

        describe('validateArrayInput', function () {
            it('throws exception when input is not an array', function () {
                expect(fn() => $this->action->handle('array-validation'))
                    ->toThrow(\InvalidArgumentException::class, 'Test array validation');
            });

            it('throws exception when required keys are missing', function () {
                // Create a test action that validates array with missing keys
                $testAction = new class extends BaseAction {
                    public function handle(mixed $input = null): mixed
                    {
                        $this->validateArrayInput($input, ['required_key']);
                        return $input;
                    }
                };

                expect(fn() => $testAction->handle(['wrong_key' => 'value']))
                    ->toThrow(\InvalidArgumentException::class, "Required key 'required_key' is missing from input array");
            });

            it('passes validation when array has required keys', function () {
                $testAction = new class extends BaseAction {
                    public function handle(mixed $input = null): mixed
                    {
                        $this->validateArrayInput($input, ['required_key']);
                        return $input;
                    }
                };

                $input = ['required_key' => 'value'];
                $result = $testAction->handle($input);
                
                expect($result)->toBe($input);
            });
        });
    });

    describe('PSR-12 compliance', function () {
        it('uses strict types declaration', function () {
            $reflection = new \ReflectionClass(BaseAction::class);
            $filename = $reflection->getFileName();
            $content = file_get_contents($filename);
            
            expect($content)->toContain('declare(strict_types=1);');
        });

        it('has proper namespace declaration', function () {
            $reflection = new \ReflectionClass(BaseAction::class);
            
            expect($reflection->getNamespaceName())->toBe('App\Actions');
        });

        it('follows proper method naming conventions', function () {
            $reflection = new \ReflectionClass(BaseAction::class);
            $methods = $reflection->getMethods();
            
            foreach ($methods as $method) {
                // Method names should be camelCase
                expect($method->getName())->toMatch('/^[a-z][a-zA-Z0-9]*$/');
            }
        });
    });
}); 