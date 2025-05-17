<?php

namespace App\Stage5;

use Exception;
use Introvert\ApiClient;
use Introvert\Configuration;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\JsonResponse;

class Controller extends AbstractController
{
    private string $introvertUrl;


    public function __construct()
    {
        $intr_key = '3363f0c5';
        $this->introvertUrl = 'https://api.yadrocrm.ru/integration/site?key=' . $intr_key;
    }
    public function index(Request $request): Response
    {
        $form1 = $this->createFormBuilder()
            ->add('NAME_MY_AN', TextType::class, [
                'attr' => ['class' => 'form-control']
            ])
            ->add('PHONE_MY_AN', TextType::class, [
                'attr' => ['class' => 'form-control']
            ])
            ->add('EMAIL_MY_AN', EmailType::class, [
                'attr' => ['class' => 'form-control']
            ])
            ->add('COMMENT_MY_AN', TextareaType::class, [
                'attr' => ['class' => 'form-control']
            ])
            ->add('STATUS_MY_AN', HiddenType::class, [
                'data' => '57230590',
            ])
            ->add('intr_group', HiddenType::class, [
                'data' => 'ske5@gmail.com;testpers3@mail.ru;bbb@bb.ru',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Submit Form 1',
                'attr' => ['class' => 'btn btn-primary']
            ])
            ->getForm();

        $form2 = $this->createFormBuilder()
           ->add('NAME_MY_AN', TextType::class, [
                'attr' => ['class' => 'form-control']
            ])
            ->add('PHONE_MY_AN', TextType::class, [
                'attr' => ['class' => 'form-control']
            ])
            ->add('EMAIL_MY_AN', EmailType::class, [
                'attr' => ['class' => 'form-control']
            ])
            ->add('COMMENT_MY_AN', TextareaType::class, [
                'attr' => ['class' => 'form-control']
            ])
            ->add('STATUS_MY_AN', HiddenType::class, [
                'data' => '57230590',
            ])
            ->add('intr_group', HiddenType::class, [
                'data' => 'ske2@gmail.com;ske1@gmail.com;deemird2@yandex.ru',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Submit Form 2',
                'attr' => ['class' => 'btn btn-primary']
            ])
            ->getForm();
        $form1->handleRequest($request);
        $form2->handleRequest($request);
        if ($form1->isSubmitted() && $form1->isValid()) {
            $formData = $form1->getData();
            return $this->callYadro($formData);
        }
        if ($form2->isSubmitted() && $form2->isValid()) {
            $formData = $form1->getData();
            return $this->callYadro($formData);
        }
        return $this->render('stage5_feature/index.html.twig', [
            'form1' => $form1->createView(),
            'form2' => $form2->createView(),
        ]);
    }
    public function callYadro($formData): JsonResponse
    {
        $cookieData = array();
        if (isset($_COOKIE['introvert_cookie'])) {
            $cookieData = json_decode($_COOKIE['introvert_cookie'], true) ?: array(); // данные сохраняемые js скриптом
        }
        $postArr = array_merge($cookieData, $formData);
        // $_POST данные отправленной формы
        // объединяем данные и отправляем
        if (function_exists('curl_init')) {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $this->introvertUrl);
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($postArr));
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_USERAGENT, 'Yadro-Site-Integration-client/1.0');
            $result = curl_exec($curl);
            curl_close($curl);
        } else {
            if ((bool) ini_get('allow_url_fopen')) {
                $opts = array(
                    'http' =>
                    array(
                        'method' => 'POST',
                        'header' => 'Content-type: application/x-www-form-urlencoded',
                        'content' => http_build_query($postArr),
                        'timeout' => 2,
                    )
                );

                try {
                    return new JsonResponse(file_get_contents($this->introvertUrl, false, stream_context_create($opts)));
                } catch (Exception $e) {
                    return new JsonResponse($e);
                }
            }
        }
        return new JsonResponse($result);
    }
}
