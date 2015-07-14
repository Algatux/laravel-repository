**Laravel Repository**

[![Build Status](https://travis-ci.org/Algatux/laravel-repository.svg)](https://travis-ci.org/Algatux/laravel-repository) [![Latest Stable Version](https://poser.pugx.org/algatux/laravel-repository/v/stable)](https://packagist.org/packages/algatux/laravel-repository) [![Total Downloads](https://poser.pugx.org/algatux/laravel-repository/downloads)](https://packagist.org/packages/algatux/laravel-repository) [![Latest Unstable Version](https://poser.pugx.org/algatux/laravel-repository/v/unstable)](https://packagist.org/packages/algatux/laravel-repository) [![License](https://poser.pugx.org/algatux/laravel-repository/license)](https://packagist.org/packages/algatux/laravel-repository)

**DOC**

Create a repository extending AbstractRepository, you only need to implement "modelClassName" method that must return the model (you want to "abstract") namespace

```

namespace Gerold\Models\Repositories;

use Algatux\Repository\Eloquent\AbstractRepository;
use Gerold\Models\User;

/**
 * Class UserRepository
 * @package Gerold\Models\Repositories
 */
class UserRepository extends AbstractRepository
{

    protected function modelClassName()
    {
        return User::class;
    }

}

```

This will instantiate a new fresh model instance of the type you secified in your repository property $model.

Obviously you can implement your methods using every standard API of Eloquent on the property.

```

    public function getAllActiveUsers()
    {
        return $this->model->where('active','=',1)->get()
    }

```

$model property is exposed by "expose()" method to use it out of the repository if you need it

```
    /** @var User $userModel **/
    $userModel = $userRepository->expose();
    
    /** OR **/
    
    $activeUsers = $userRepository->expose()->where('active','=',1)->get();

```
