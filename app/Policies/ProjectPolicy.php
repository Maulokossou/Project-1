<?php

namespace App\Policies;

use App\Models\Association;
use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProjectPolicy
{
    use HandlesAuthorization;

    public function view(Association $association, Project $project)
    {
        return $association->id === $project->association_id;
    }

    public function update(Association $association, Project $project)
    {
        return $association->id === $project->association_id;
    }

    public function delete(Association $association, Project $project)
    {
        return $association->id === $project->association_id;
    }
}