<?php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('full_name',TextType::class,[
                'attr' => [
                    'placeholder' => 'Votre nom & prénom...',
                    'class' => 'form-control mb-3'
                ],
                'required' => true,
                'label' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un nom complet',
                    ]),
                ],
            ])
            ->add('email',EmailType::class,[
                'attr' => [
                    'placeholder' => 'Votre email...',
                    'class' => 'form-control mb-3'
                ],
                'label' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un email',
                    ]),
                ],
                'required' => true,
            ])
            ->add('subject',TextType::class,[
                'attr' => [
                    'placeholder' => 'Sujet...',
                    'class' => 'form-control mb-3'
                ],
                'label' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un sujet',
                    ]),
                ],
                'required' => true,
            ])
            ->add('message',TextareaType::class,[
                'attr' => [
                    'placeholder' => 'Votre message...',
                    'class' => 'form-control mb-3',
                    'rows' => 5,
                    'cols' => 5
                ],
                'label' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un message',
                    ]),
                ],
                'required' => true,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Envoyer',
                'attr' => [
                    'class' => 'btn btn-primary'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
