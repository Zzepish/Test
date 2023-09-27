<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Type\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[ Route(path: "/api/auth", name: "auth") ]
class CustomAuthController extends AbstractController
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly EntityManagerInterface $entityManager,
        private readonly JWTTokenManagerInterface $JWTManager
    )
    {}

    #[Route(path: "/login", name: "_login", methods: ["POST"])]
    public function login(Request $request): JsonResponse
    {
        $userRepository = $this->entityManager->getRepository(User::class);
        $user = $userRepository->findOneBy([
            'email' => $request->get('email')
        ]);

        if($user) {
            if($this->passwordHasher->isPasswordValid($user, $request->get('password'))) {
                return $this->json([
                    'status' => 'success',
                    'token' => $this->JWTManager->create($user)
                ]);
            }
        }

        return $this->json([
            'status' => 'fail',
            'message' => 'invalid_credentials'
        ]);
    }

    #[Route(path: "/register", name: "_register", methods: ["POST"])]
    public function register(Request $request): JsonResponse
    {
        $form = $this->createForm(UserType::class, new User());
        $form->handleRequest($request);

        if($form->isSubmitted() and $form->isValid()) {
            $userRepository = $this->entityManager->getRepository(User::class);
            $user = $form->getData();
            $userRepository->updatePassword($user, $user->getPassword());
            return $this->json([
                'token' => $this->JWTManager->create($user)
            ]);
        }

        return $this->json($this->getFormErrors($form));
    }

    #[Route(path: "/test", name: "_test"), IsGranted("ROLE_USER")]
    public function test(): JsonResponse
    {
        return $this->json($this->getUser());
    }

    private function getFormErrors(FormInterface $form): array
    {
        $errors = [];

        foreach ($form->getErrors(true, true) as $error) {
            $errors[$form->getName()][] = $error->getMessage();
        }

        return $errors;
    }
}