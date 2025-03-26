<?php

namespace App\Repositories;

use App\Models\Video;
use App\Interfaces\VideoInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;

class VideoRepository implements VideoInterface
{
    public function create(array $data)
    {
        try {
            return Video::create($data);
        } catch (QueryException $e) {
            throw new \Exception("Une erreur s'est produite lors de la création de la vidéo.");
        }
    }

    public function getVideosByCourseId($courseId)
    {
        try {
            return Video::where('course_id', $courseId)->get();
        } catch (QueryException $e) {
            throw new \Exception("Une erreur s'est produite lors de la récupération des vidéos.");
        }
    }

    public function findById($videoId)
    {
        try {
            return Video::findOrFail($videoId);
        } catch (ModelNotFoundException $e) {
            throw new \Exception("Vidéo non trouvée avec l'ID : $videoId");
        } catch (QueryException $e) {
            throw new \Exception("Une erreur s'est produite lors de la récupération de la vidéo.");
        }
    }

    public function update($videoId, array $data)
    {
        try {
            $video = Video::findOrFail($videoId);
            $video->update($data);
            return $video;
        } catch (ModelNotFoundException $e) {
            throw new \Exception("Vidéo non trouvée avec l'ID : $videoId");
        } catch (QueryException $e) {
            throw new \Exception("Une erreur s'est produite lors de la mise à jour de la vidéo.");
        }
    }

    public function delete($videoId)
    {
        try {
            $video = Video::findOrFail($videoId);
            $video->delete();
            return true;
        } catch (ModelNotFoundException $e) {
            throw new \Exception("Vidéo non trouvée avec l'ID : $videoId");
        } catch (QueryException $e) {
            throw new \Exception("Une erreur s'est produite lors de la suppression de la vidéo.");
        }
    }
}