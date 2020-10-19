<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends APIController
{
    private $manager;

    private $userRepo;

    public function __construct(EntityManagerInterface $manager, UserRepository $userRepo)
    {
        $this->manager = $manager;
        $this->userRepo = $userRepo;
    }
    
    /**
    * @Route("/users", methods="GET")
    */
    public function index()
    {
        $users = $this->userRepo->transformAll();

        return $this->respond($users);
    }

    /**
    * @Route("/register", methods="POST")
    */
    public function create(Request $request, UserPasswordEncoderInterface $encode)
    {
        $request = $this->transformJsonBody($request);

        if (!$request) {
            return $this->respondValidationError('Please provide a valid request!');
        }

        // validate the firstName
        if (!$request->get('firstName')) {
            return $this->respondValidationError('Please provide a Firstname!');
        }

        if (strlen($request->get('firstName')) > 30) {
            return $this->respondValidationError('Vous ne pouvez pas choisir plus de 30 caractères firstName');
        }
        if (strlen($request->get('firstName')) < 5) {
            return $this->respondValidationError('Vous devez choisir au moins 5 caractères firstName');
        }


        // validate the lastName
        if (!$request->get('lastName')) {
            return $this->respondValidationError('Please provide a Lastname!');
        }

        if (strlen($request->get('lastName')) > 30) {
            return $this->respondValidationError('Vous ne pouvez pas choisir plus de 30 caractères lastName');
        }
        if (strlen($request->get('lastName')) < 5) {
            return $this->respondValidationError('Vous devez choisir au moins 5 caractères lastName');
        }

        // validate the email
        $email = $request->get('email');

        $user = $this->userRepo->findOneByEmail($email);

        if ($user !== null) {
            return $this->respondValidationError('email : [Cette adresse email existe déjà.]');
        }
        

        // persist the new movie
        $user = new User;

        $hash = $encode->encodePassword($user, $request->get('password'));

        $user->setFirstName($request->get('firstName'))
             ->setLastName($request->get('lastName'))
             ->setPassword($hash)
             ->setEmail($request->get('email'));

        $this->manager->persist($user);
        $this->manager->flush();

        return $this->respondCreated($this->userRepo->transform($user));
    }

    /**
    * @Route("/api/user", methods="GET")
    */
    public function userAccount(Request $request)
    {
       /* $user = $this->userRepo->findOneById(1);
        // $userId = $request->get('user')->getId();

        $user = $this->userRepo->findOneById(1);
        // $user = $this->userRepo->findOneById($userId);
        
        $userSerialise = $this->userRepo->transform($user);

        dd($userSerialise);

        return $this->respond($userSerialise);*/
        // dd($request->headers->get('Authorization'), $request->headers);

        $token = $request->headers->get('Authorization');

        $tokenParts = explode(".", $token);  
        $tokenHeader = base64_decode($tokenParts[0]);
        $tokenPayload = base64_decode($tokenParts[1]);
        $jwtHeader = json_decode($tokenHeader);
        $jwtPayload = json_decode($tokenPayload);

        /*
            $user = [
                'username' => (string) $jwtPayload->username,
                'firstName' => (string) $jwtPayload->firstName,
                'latsName' => (string) $jwtPayload->lastName
            ];
        */

        // $user['user'] = $this->userRepo->findOneByUserJwt($jwtPayload);
        $user = $this->userRepo->findOneByUserJwt($jwtPayload);

        /*$user = [
            'username' => $jwtPayload->username,
            'firstName' => $jwtPayload->firstName,
            'latsName' => $jwtPayload->lastName
        ];*/

        // dd($user);

        return $this->respond($user);

        // dd($jwtPayload/*, $user*/);
        
    }

}
