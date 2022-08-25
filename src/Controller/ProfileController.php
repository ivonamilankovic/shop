<?php

namespace App\Controller;

use App\Form\ChangePasswordFormType;
use App\Form\UpdateProfileFormType;
use App\Form\UploadProfileImageFormType;
use App\Service\UploadImagesHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{
    /**
     * @Route("/profile", name="app_profile")
     */
    public function profile(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $hasher, UploadImagesHelper $uploadImagesHelper):Response
    {
        $user = $this->getUser();

        $forms = [
            'formUploadImage' => $this->createForm(UploadProfileImageFormType::class),
            'formProfileInfo' => $this->createForm(UpdateProfileFormType::class, $user),
            'formChangePassword' => $this->createForm(ChangePasswordFormType::class)
        ];
        foreach ($forms as $form) {
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){
                /** @var UploadedFile $uploadedFile */
                //TODO kako razlikovati submitove?
                if($form->get('Upload_profile_picture')->isClicked()){
                    $uploadedFile = $form['picture']->getData();
                    $newFileName = $uploadImagesHelper->uploadProfileImage($uploadedFile);
                    $user->setPicture($newFileName);
                }
                if($form->get('Change')->isClicked()){
                    $plainPassword = $form->get('password')->getData();
                    $hashedPassword = $hasher->hashPassword($user, $plainPassword);
                    $user->setPassword($hashedPassword);
                }
                $em->persist($user);
                $em->flush();
                $this->addFlash('success', 'Your data has been updated successfully.');
            }
        }
        return $this->render('profile/userProfile.html.twig',[
            'forms' => array_map(
                function ($form){
                    return $form->createView();
                }, $forms
            )
        ]);

            /*   $formProfileInfo = $this->createForm(UpdateProfileFormType::class, $user);
                $formProfileInfo->handleRequest($request);
                if($formProfileInfo->isSubmitted() && $formProfileInfo->isValid()){
                    $em->persist($user);
                    $em->flush();
                    $this->addFlash('success', 'Your data has been updated successfully.');
                }
$formProfileInfo->

                $formUploadImage = $this->createForm(UploadProfileImageFormType::class);
                $formUploadImage->handleRequest($request);
                if($formUploadImage->isSubmitted() && $formUploadImage->isValid()){
                    /** @var UploadedFile $uploadedFile */
        /*    $uploadedFile = $request->files->get('image');
            if($uploadedFile){
                $newFileName = $uploadImagesHelper->uploadProfileImage($uploadedFile);
                $user->setPicture($newFileName);
                $em->persist($user);
                $em->flush();
                $this->addFlash('success', 'Your data has been updated successfully.');
            }
        }


        return $this->render('profile/userProfile.html.twig', [
            'formProfileInfo' => $formProfileInfo->createView(),
            'formUploadImage' => $formUploadImage->createView()
        ]);*/
    }


}