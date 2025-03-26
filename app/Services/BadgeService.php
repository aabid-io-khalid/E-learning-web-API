<?php

namespace App\Services;

use Exception;
use App\Models\User;
use App\Models\Badge;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BadgeService
{
    public function checkAndAssignBadges(User $user)
    {
        $this->checkStudentBadges($user);
        
        if ($user->is_mentor) {
            $this->checkMentorBadges($user);
        }
    }

    public function checkStudentBadges($id)
    {
        $badges = [];

        try {
            $user = User::findOrFail($id);  
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Utilisateur non trouvé'], 404);
        }

        $completedCourses = Enrollment::where('user_id', $id)->count();
    
        if ($completedCourses >= 1) {
            $this->assignBadge($user, 'Débutant');
            $badges[] = 'Débutant';
        }
    
        if ($completedCourses >= 5) {
            $this->assignBadge($user, 'Intermédiaire');
            $badges[] = 'Intermédiaire';
        }
    
        if ($completedCourses >= 10) {
            $this->assignBadge($user, 'Expert');
            $badges[] = 'Expert';
        }
    
        return $badges;
    }
    

    protected function checkMentorBadges(User $user)
    {
        $createdCourses = $user->createdCourses()->count();
        $studentsCount = $user->createdCourses()->withCount('students')->get()->sum('students_count');

        // Badge pour 5 cours créés
        if ($createdCourses >= 5) {
            $this->assignBadge($user, 'Créateur de contenu');
        }

        // Badge pour 50 élèves
        if ($studentsCount >= 50) {
            $this->assignBadge($user, 'Mentor populaire');
        }
    }

    protected function assignBadge(User $user, $badgeName)
    {
        $badge = Badge::where('name', $badgeName)->first();

        if ($badge && !$user->badges->contains($badge->id)) {
            $user->badges()->attach($badge->id);
        }
    }

    public function getUserBadges(User $user)
    {
        return $user->badges;
    }

    // creer un badge par l'admin ( le role est verifié par middlware)


    

    // delete badge
    public function deleteBadge($id)
    {
        try {

            $badge = Badge::findOrFail($id);

            $badge->delete();

            return response()->json(['message' => 'Badge supprimé avec succès']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }


}