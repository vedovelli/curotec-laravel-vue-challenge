<?php

declare(strict_types=1);

namespace App\Actions\Contracts;

/**
 * Action Interface
 * 
 * Defines the contract that all action classes must implement.
 * Actions are single-purpose classes that encapsulate business logic
 * and follow the single responsibility principle.
 * 
 * @template TInput
 * @template TOutput
 */
interface ActionInterface
{
    /**
     * Handle the action execution.
     * 
     * This method should contain the main business logic of the action.
     * It should be the only public method in action classes.
     * 
     * @param TInput $input The input data for the action
     * @return TOutput The result of the action execution
     */
    public function handle(mixed $input = null): mixed;
} 