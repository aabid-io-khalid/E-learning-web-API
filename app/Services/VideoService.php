<?php

namespace App\Services;

use App\Repositories\VideoRepository;
use App\Models\Video;

class VideoService
{
    protected $videoRepository;

    public function __construct(VideoRepository $videoRepository)
    {
        $this->videoRepository = $videoRepository;
    }

    public function addVideoToCourse($courseId, array $data)
    {
        $data['course_id'] = $courseId;
        return $this->videoRepository->create($data);   
    }

    public function getVideosByCourseId($courseId)
    {
        return $this->videoRepository->getVideosByCourseId($courseId);
    }

    public function getVideoById($videoId)
    {
        return $this->videoRepository->findById($videoId);
    }

    public function updateVideo($videoId, array $data)
    {
        return $this->videoRepository->update($videoId, $data);
    }

    public function deleteVideo($videoId)
    {
        return $this->videoRepository->delete($videoId);
    }
}