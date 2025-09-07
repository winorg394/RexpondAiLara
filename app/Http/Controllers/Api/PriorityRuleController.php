<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PriorityRule;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class PriorityRuleController extends Controller
{
    /**
     * Display a listing of the priority rules.
     */
    public function index(): JsonResponse
    {
        $rules = PriorityRule::all();
        return $this->reply(true, 'Priority rules retrieved successfully', ['rules' => $rules]);
    }

    /**
     * Store a newly created priority rule.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'rule_name' => 'required|string|max:255',
            'condition' => 'required|string|in:contains,does_not_contain,equals,starts_with,ends_with',
            'keywords' => 'required|array|min:1',
            'keywords.*' => 'string|max:255',
            'action' => 'required|in:' . implode(',', [
                PriorityRule::ACTION_SET_PRIORITY,
                PriorityRule::ACTION_MARK_AS_SPAN,
            ]),
            'priority_type' => 'required_if:action,' . PriorityRule::ACTION_SET_PRIORITY . '|in:' . implode(',', [
                PriorityRule::PRIORITY_HIGH,
                PriorityRule::PRIORITY_MID,
                PriorityRule::PRIORITY_LOW,
            ]),
        ]);

        if ($validator->fails()) {
            return $this->reply(false, 'Validation error', ['errors' => $validator->errors()], 422);
        }

        $data = $request->all();
        $data['keywords'] = $request->input('keywords');

        $rule = PriorityRule::create($data);

        return $this->reply(true, 'Priority rule created successfully', ['rule' => $rule], 201);
    }

    /**
     * Display the specified priority rule.
     */
    public function show(PriorityRule $priorityRule): JsonResponse
    {
        return $this->reply(true, 'Priority rule retrieved successfully', ['rule' => $priorityRule]);
    }

    /**
     * Update the specified priority rule.
     */
    public function update(Request $request, PriorityRule $priorityRule): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'rule_name' => 'sometimes|required|string|max:255',
            'condition' => 'sometimes|required|string|in:contains,does_not_contain,equals,starts_with,ends_with',
            'keywords' => 'sometimes|required|array|min:1',
            'keywords.*' => 'string|max:255',
            'action' => 'sometimes|required|in:' . implode(',', [
                PriorityRule::ACTION_SET_PRIORITY,
                PriorityRule::ACTION_MARK_AS_SPAN,
            ]),
            'priority_type' => 'required_if:action,' . PriorityRule::ACTION_SET_PRIORITY . '|in:' . implode(',', [
                PriorityRule::PRIORITY_HIGH,
                PriorityRule::PRIORITY_MID,
                PriorityRule::PRIORITY_LOW,
            ]),
        ]);

        if ($validator->fails()) {
            return $this->reply(false, 'Validation error', ['errors' => $validator->errors()], 422);
        }

        $data = $request->all();
        if ($request->has('keywords')) {
            $data['keywords'] = $request->input('keywords');
        }

        $priorityRule->update($data);

        return $this->reply(true, 'Priority rule updated successfully', ['rule' => $priorityRule]);
    }

    /**
     * Remove the specified priority rule.
     */
    public function destroy(PriorityRule $priorityRule): JsonResponse
    {
        $priorityRule->delete();
        return $this->reply(true, 'Priority rule deleted successfully');
    }

    /**
     * Get available actions for priority rules.
     */
    public function getActions(): JsonResponse
    {
        return $this->reply(true, 'Available actions retrieved', [
            'actions' => PriorityRule::getActions()
        ]);
    }

    /**
     * Get available priority types.
     */
    public function getPriorityTypes(): JsonResponse
    {
        return $this->reply(true, 'Available priority types retrieved', [
            'priority_types' => PriorityRule::getPriorityTypes()
        ]);
    }
}
