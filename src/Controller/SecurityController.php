<?php

declare(strict_types=1);

namespace App\Controller;


use App\Entity\User;
use App\Form\Type\LoginType;
use App\Security\UpdateProfileType;
use Doctrine\ORM\EntityManagerInterface;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class SecurityController
 * @package App\Controller
 * @author Ruslan Valis <ruslan.valis@itomy.ch>
 */
class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function loginAction(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('api_entrypoint');
        }
        $form = $this->createForm(LoginType::class);
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('security/login.html.twig', [
            'form' => $form->createView(),
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    /**
     * @Route("/profile", name="app_profile")
     * @param Request $request
     * @param UserPasswordEncoderInterface $userPasswordEncoder
     * @param ValidatorInterface $validator
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function updateProfileAction(
        Request $request,
        UserPasswordEncoderInterface $userPasswordEncoder,
        ValidatorInterface $validator,
        EntityManagerInterface $em): Response
    {
        // Validate access rules
        $this->denyAccessUnlessGranted('ROLE_USER');

        // Redirect user to login if can't get it properly
        /** @var User $user */
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // Create form and handle request
        $form = $this->createForm(UpdateProfileType::class, [
            'username' => $user->getUsername()
        ]);
        $form->handleRequest($request);

        // Process data from the form
        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            // Update username
            if (!empty($formData['username'])) {
                $user->setUsername($formData['username']);
                $errors = $validator->validate($user);
                if (count($errors) > 0) {
                    $this->addFlash('error', $errors->get(0)->getMessage());
                    return $this->redirectToRoute('app_profile');
                }
            }
            // Update password
            if (!empty($formData['password'])) {
                $user->setPassword($userPasswordEncoder->encodePassword($user, $formData['password']));
            }

            // Persist changes to database
            $em->flush();

            // Notify about successful update
            $this->addFlash('notice','Updated successfully!');
            return $this->redirectToRoute('app_profile');
        }

        // Render profile template
        return $this->render('security/profile.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(): void
    {
        throw new RuntimeException('Should be intercepted by firewall');
    }
}
