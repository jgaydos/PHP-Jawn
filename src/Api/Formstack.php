<?php

namespace Jawn\Api;

/**
 * Formstack API
 * https://developers.formstack.com/docs/getting-started
 */
class Formstack
{
    private $_token; // API v2

    /**
     * Set token
     */
    public function __construct(string $token)
    {
        $this->_token = $token;
    }

    /**
     * Returns array of all form meta info
     * @return  array   $array  Array of forms
     */
    public function forms(): array
    {
        $url = 'https://www.formstack.com/api/v2/form.json';
        $data = $this->curl($url);

        $ofTheKing = [];
        foreach ($data as $row) {
            if (!is_array($row)) {
                continue;
            }

            foreach ($row as $field)
            {
                $ofTheKing[$field->id] = [
                    'id' => $field->id,
                    'name' => $field->name,
                    'url' => $field->url,
                    'submissions' => $field->submissions,
                    'last_submission_id' => $field->last_submission_id,
                    'views' => $field->views,
                    'last_submission_time' => $field->last_submission_time ?? ''
                ];
            }
        }

        return $ofTheKing;
    }

    /**
     * Returns detailed for data
     * @param	string	$formID		Vender form ID
     * @return  json   $json  Form meta info
     */
    public function form(int $formID): array
    {
        $url = "https://www.formstack.com/api/v2/form/{$formID}.json";
        $ofTheKing = $this->curl($url);
        return $ofTheKing;
    }

    /**
     * Returns general data on for submissions
     * @param	array	$params		Parameters array
     * @return  array   $array  Array of submissions
     */
    public function submissions(int $formID): array
    {
        $page = 1;
        $limit = 100;
        $ofTheKing = [];

        do {
            $url = "https://www.formstack.com/api/v2/form/{$formID}/submission.json?page={$page}&per_page={$limit}";
            $result = $this->curl($url);
            $submissions = $result->submissions;
            $pages = $result->pages;

            foreach ($submissions as $submission) {
                $ofTheKing[$submission->id] = [
                    'id' => $submission->id,
                    'timestamp' => $submission->timestamp,
                    'user_agent' => $submission->user_agent,
                    'payment_status' => $submission->payment_status,
                    'remote_addr' => $submission->remote_addr,
                    'latitude' => $submission->latitude,
                    'longiude' => $submission->longitude
                ];
            }
            ++$page;
        } while ($page <= $pages);

        return $ofTheKing;
    }

    /**
     * Returns columns
     * @param	array	$params		Parameters array
     * @return  array   $array  Array of columns
     */
    public function columns(int $formID):array
    {
        $url = "https://www.formstack.com/api/v2/form/{$formID}.json";
        $fields = $this->curl($url)->fields;
        return $fields;
    }

    /**
     * Returns detailed submission data
     *
     * @param	array	$params		Parameters array
     *
     * @return  array   $array  Array of submission responses
     */
    public function submission(int $submissionID): array
    {
        $url = "https://www.formstack.com/api/v2/submission/{$submissionID}.json";
        $data = $this->curl($url)->data;
        $ofTheKing = [];
        foreach ($data as $field) {
            $ofTheKing[$field->field] = $field->value;
        }
        return $ofTheKing;
    }

    private function curl(string $url)
    {
        return json_decode(\Jawn\Remote\Curl::get(
            $url,
            null,
            [
                'Accept: application/json',
                'Content-type: application/json',
                "Authorization: Bearer {$this->_token}"
            ]
        )['content']);
    }
}
