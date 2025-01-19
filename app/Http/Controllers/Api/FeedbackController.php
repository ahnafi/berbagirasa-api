<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\FeedbackResource;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FeedbackController extends Controller
{
    /**
     * index
     *
     * @return void
     */
    public function index() {
        $feedback = Feedback::all();

        return new FeedbackResource('success', 'Data fetched successfully', $feedback);
    }

    /**
     * store
     *
     * @param  mixed $request
     * @return void
     */
    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:3|max:255',
            'email' => 'required|email',
            'message' => 'required|string',
        ]);

        if ($validator->fails()) {
            return new FeedbackResource('error', $validator->errors(), null);
        }

        $feedback = Feedback::create($request->all());

        return new FeedbackResource('success', 'Feedback created successfully', $feedback);
    }

    /**
     * show
     *
     * @param  mixed $id
     * @return void
     */
    public function show($id)
    {
        $feedback = Feedback::find($id);

        if (!$feedback) {
            return new FeedbackResource('error', 'Feedback not found', null);
        }

        return new FeedbackResource('success', 'Feedback fetched successfully', $feedback);
    }

    /**
     * update
     *
     * @param  mixed $request
     * @param  mixed $id
     * @return void
     */
    public function update(Request $request, $id)
    {
        $feedback = Feedback::find($id);

        if (!$feedback) {
            return new FeedbackResource('error', 'Feedback not found', null);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:3|max:255',
            'email' => 'required|email',
            'message' => 'required|string',
        ]);

        if ($validator->fails()) {
            return new FeedbackResource('error', $validator->errors(), null);
        }

        $feedback->update($request->all());

        return new FeedbackResource('success', 'Feedback updated successfully', $feedback);
    }

    /**
     * delete
     *
     * @param  mixed $id
     * @return void
     */
    public function destroy($id)
    {
        $feedback = Feedback::find($id);

        if (!$feedback) {
            return new FeedbackResource('error', 'Feedback not found', null);
        }

        $feedback->delete();

        return new FeedbackResource('success', 'Feedback deleted successfully', null);
    }
}
