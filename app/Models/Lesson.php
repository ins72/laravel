<?php

namespace App\Models;

use App\Models\CoursesLesson;

class Lesson extends CoursesLesson
{
    // This model extends CoursesLesson to provide a cleaner API interface
    // All functionality is inherited from CoursesLesson
    
    protected $table = 'courses_lessons';

    // Add any additional API-specific methods here if needed
    
    /**
     * Get the course that owns the lesson
     */
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    /**
     * Get lesson content in a structured format
     */
    public function getStructuredContent()
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'video_url' => $this->video_url,
            'duration' => $this->duration,
            'order' => $this->order,
            'status' => $this->status,
            'media' => $this->media,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
} 