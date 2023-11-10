<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSubmissionAnswerRequest;
use App\Http\Requests\UpdateSubmissionAnswerRequest;
use App\Models\SubmissionAnswer;

class SubmissionAnswerController extends APIController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreSubmissionAnswerRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreSubmissionAnswerRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SubmissionAnswer  $submissionAnswer
     * @return \Illuminate\Http\Response
     */
    public function show(SubmissionAnswer $submissionAnswer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\SubmissionAnswer  $submissionAnswer
     * @return \Illuminate\Http\Response
     */
    public function edit(SubmissionAnswer $submissionAnswer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateSubmissionAnswerRequest  $request
     * @param  \App\Models\SubmissionAnswer  $submissionAnswer
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateSubmissionAnswerRequest $request, SubmissionAnswer $submissionAnswer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SubmissionAnswer  $submissionAnswer
     * @return \Illuminate\Http\Response
     */
    public function destroy(SubmissionAnswer $submissionAnswer)
    {
        //
    }
}
