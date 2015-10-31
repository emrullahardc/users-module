<?php namespace Anomaly\UsersModule\Role;

use Anomaly\Streams\Platform\Entry\EntryCollection;
use Anomaly\Streams\Platform\Entry\EntryRepository;
use Anomaly\UsersModule\Role\Contract\RoleInterface;
use Anomaly\UsersModule\Role\Contract\RoleRepositoryInterface;
use Illuminate\Support\Collection;

/**
 * Class RoleRepositoryInterface
 *
 * @link          http://anomaly.is/streams-platform
 * @author        AnomalyLabs, Inc. <hello@anomaly.is>
 * @author        Ryan Thompson <ryan@anomaly.is>
 * @package       Anomaly\UsersModule\RoleInterface
 */
class RoleRepository extends EntryRepository implements RoleRepositoryInterface
{

    /**
     * The role model.
     *
     * @var RoleModel
     */
    protected $model;

    /**
     * Create a new RoleRepositoryInterface instance.
     *
     * @param RoleModel $model
     */
    function __construct(RoleModel $model)
    {
        $this->model = $model;
    }

    /**
     * Return all but the admin role.
     *
     * @return RoleCollection
     */
    public function allButAdmin()
    {
        return $this->model->where('slug', '!=', 'admin')->get();
    }

    /**
     * Find a role by it's slug.
     *
     * @param $slug
     * @return null|RoleInterface
     */
    public function findBySlug($slug)
    {
        return $this->model->where('slug', $slug)->first();
    }

    /**
     * Find a role by a permission key.
     *
     * @param $permission
     * @return null|EntryCollection
     */
    public function findByPermission($permission)
    {
        $query = $this->model->newQuery();

        foreach ((array)$permission as $key) {
            $query->where('permissions', 'LIKE', '%"' . str_replace('*', '%', $key) . '"%');
        }

        return $query->get();
    }

    /**
     * Update permissions for a role.
     *
     * @param RoleInterface $role
     * @param array         $permissions
     * @return RoleInterface
     */
    public function updatePermissions(RoleInterface $role, array $permissions)
    {
        $role->permissions = $permissions;

        $role->save();

        return $role;
    }
}
