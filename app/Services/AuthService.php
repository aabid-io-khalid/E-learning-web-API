<?php
namespace App\Services;

use App\Interfaces\AuthRepositoryInterface;

class AuthService
{
    protected $authRepository;

    public function __construct(AuthRepositoryInterface $authRepository)
    {
        $this->authRepository = $authRepository;
    }

    public function register(array $data)
    {
        try{
            return $this->authRepository->register($data);

        } catch (\Throwable $th) {
            throw new \Exception("Erreur lors de register : " . $th->getMessage());
        }
        
    }

    public function login(array $credentials)
    {
        try{
            return $this->authRepository->login($credentials);
        } catch (\Throwable $th) {
            throw new \Exception("Erreur lors de login : " . $th->getMessage());
        }
        
    }

    public function logout()
    { 
        try{
            return $this->authRepository->logout();

        } catch (\Throwable $th) {
            throw new \Exception("Erreur lors de logout : " . $th->getMessage());
        }
    }
}