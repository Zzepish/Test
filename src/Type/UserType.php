<?php

namespace App\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', TextType::class, [
                'invalid_message'   => 'general.email.incorrect',
                'constraints'       => [
                    new NotBlank([
                        'message' => 'general.email.is_blank',
                    ]),
                    new Email([
                        'message' => 'general.email.incorrect',
                    ]),
                ],
            ])
            ->add('password', RepeatedType::class, [
                'type'              => PasswordType::class,
                'invalid_message'   => 'general.password.must_match',
                'first_name'        => 'password',
                'second_name'       => 'password_repeat',
                'constraints'       => [
                    new NotBlank([
                        'message' => 'general.password.is_blank',
                    ]),
                    new Length([
                        'min'        => 6,
                        'minMessage' => 'general.password.short',
                    ])
                ]
            ])
            ->add('company', TextType::class, [
                'trim' => true,
                'constraints' => [
                    new NotBlank(['message' => 'general.company_name.ib_blank'])
                ],
            ])
            ->add('privacyPolicy', CheckboxType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'general.privacy_policy.not_confirmed'])
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection'   => false,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'user';
    }
}