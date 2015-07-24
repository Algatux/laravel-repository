##Laravel Repository

[![Build Status](https://travis-ci.org/Algatux/laravel-repository.svg)](https://travis-ci.org/Algatux/laravel-repository) [![Latest Stable Version](https://poser.pugx.org/algatux/laravel-repository/v/stable)](https://packagist.org/packages/algatux/laravel-repository) [![Total Downloads](https://poser.pugx.org/algatux/laravel-repository/downloads)](https://packagist.org/packages/algatux/laravel-repository) [![Latest Unstable Version](https://poser.pugx.org/algatux/laravel-repository/v/unstable)](https://packagist.org/packages/algatux/laravel-repository) [![License](https://poser.pugx.org/algatux/laravel-repository/license)](https://packagist.org/packages/algatux/laravel-repository)

###Documentation

####Features

Laravel Repository is a very simple shortcut to mantain organized models that you use around of your projects. 
Defining your "library" of interrogation methods in one unique class you'll be able to simply maintain them.
This Repository pattern implementation comes with caching possibility out of the box, allowing you to skip database interrogation when not really needed.
There is an option to define some Criteria classes (actually under development and not working very well) that will leto you to define reusable sets of conditions whereby interrogate your models.

####Basic Usage

Create a new class repository extending AbstractRepository, then you only need to implement a "modelClassName" method that will return the target model namespace (the one that you want to "abstract").

``` php
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

``` php
    public function getAllActiveUsers()
    {
        return $this->model->where('active','=',1)->get()
    }
    
    // OR 
    
    public function getUsersByName($name)
    {
        /** @var Builder $qb */
        $qb = $this->getDefaultQueryBuilder();
        $qb->where('name','LIKE', '%' . $name . '%');
        return $qb->get();
    }
```

$model property is exposed by the "expose()" method to use it out of the repository if you need it

``` php
    /** @var User $userModel **/
    $userModel = $userRepository->expose();
    //OR
    $activeUsers = $userRepository->expose()->where('active','=',1)->get();
```

####Caching Results

As previously said there is the option to mantain previously executed queries in cache. This is achieved by using the two methods useCacheResult() and getResults().

``` php
    public function getUsersByName($name)
    {
        /** @var Builder $qb */
        $qb = $this->getDefaultQueryBuilder();
        $qb->where('name','LIKE', '%' . $name . '%');
        return $this->useCacheResult()->getResults($qb);
    }
```

-useCacheResult( boolean $use=true, int $cacheLifeTime = 1 )
// cache life time as laravel convention is represented in minutes

####Criteria
----








