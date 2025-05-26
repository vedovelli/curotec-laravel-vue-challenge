<?php

declare(strict_types=1);

namespace App\Actions;

use App\Actions\Contracts\ActionInterface;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Base Action Class
 * 
 * Abstract base class that provides common functionality for all action classes.
 * This class implements the ActionInterface and provides a foundation for
 * concrete action implementations.
 * 
 * Key principles:
 * - Single responsibility: Each action should do one thing well
 * - Immutability: Actions should be read-only (no property mutations)
 * - Type safety: Proper type declarations for all methods
 * - Error handling: Consistent error handling across all actions
 */
abstract class BaseAction implements ActionInterface
{
    /**
     * Handle the action execution.
     * 
     * This method must be implemented by concrete action classes.
     * It should contain the main business logic of the action.
     * 
     * @param mixed $input The input data for the action
     * @return mixed The result of the action execution
     */
    abstract public function handle(mixed $input = null): mixed;

    /**
     * Execute the action with error handling and logging.
     * 
     * This method provides a wrapper around the handle method with
     * consistent error handling and logging capabilities.
     * 
     * @param mixed $input The input data for the action
     * @return mixed The result of the action execution
     * @throws Throwable Re-throws any exceptions after logging
     */
    public function execute(mixed $input = null): mixed
    {
        try {
            $this->logActionStart($input);
            $result = $this->handle($input);
            $this->logActionSuccess($result);
            
            return $result;
        } catch (Throwable $exception) {
            $this->logActionError($exception, $input);
            throw $exception;
        }
    }

    /**
     * Get the action name for logging purposes.
     * 
     * @return string The class name without namespace
     */
    protected function getActionName(): string
    {
        return class_basename(static::class);
    }

    /**
     * Log the start of action execution.
     * 
     * @param mixed $input The input data
     * @return void
     */
    protected function logActionStart(mixed $input): void
    {
        if (config('app.debug')) {
            Log::debug("Action started: {$this->getActionName()}", [
                'action' => $this->getActionName(),
                'input_type' => gettype($input),
                'timestamp' => now()->toISOString(),
            ]);
        }
    }

    /**
     * Log successful action execution.
     * 
     * @param mixed $result The action result
     * @return void
     */
    protected function logActionSuccess(mixed $result): void
    {
        if (config('app.debug')) {
            Log::debug("Action completed successfully: {$this->getActionName()}", [
                'action' => $this->getActionName(),
                'result_type' => gettype($result),
                'timestamp' => now()->toISOString(),
            ]);
        }
    }

    /**
     * Log action execution errors.
     * 
     * @param Throwable $exception The exception that occurred
     * @param mixed $input The input data that caused the error
     * @return void
     */
    protected function logActionError(Throwable $exception, mixed $input): void
    {
        Log::error("Action failed: {$this->getActionName()}", [
            'action' => $this->getActionName(),
            'exception' => $exception->getMessage(),
            'exception_class' => get_class($exception),
            'input_type' => gettype($input),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Validate that the input is not null when required.
     * 
     * @param mixed $input The input to validate
     * @param string $message Custom error message
     * @return void
     * @throws \InvalidArgumentException When input is null but required
     */
    protected function validateInputNotNull(mixed $input, string $message = 'Input cannot be null'): void
    {
        if ($input === null) {
            throw new \InvalidArgumentException($message);
        }
    }

    /**
     * Validate that the input is of the expected type.
     * 
     * @param mixed $input The input to validate
     * @param string $expectedType The expected type
     * @param string $message Custom error message
     * @return void
     * @throws \InvalidArgumentException When input type doesn't match
     */
    protected function validateInputType(mixed $input, string $expectedType, string $message = ''): void
    {
        $actualType = gettype($input);
        
        if ($actualType !== $expectedType) {
            $message = $message ?: "Expected input of type {$expectedType}, got {$actualType}";
            throw new \InvalidArgumentException($message);
        }
    }

    /**
     * Validate that the input is an array with required keys.
     * 
     * @param mixed $input The input to validate
     * @param array<string> $requiredKeys The required array keys
     * @param string $message Custom error message
     * @return void
     * @throws \InvalidArgumentException When validation fails
     */
    protected function validateArrayInput(mixed $input, array $requiredKeys = [], string $message = ''): void
    {
        if (!is_array($input)) {
            $message = $message ?: 'Input must be an array';
            throw new \InvalidArgumentException($message);
        }

        foreach ($requiredKeys as $key) {
            if (!array_key_exists($key, $input)) {
                $message = $message ?: "Required key '{$key}' is missing from input array";
                throw new \InvalidArgumentException($message);
            }
        }
    }
} 