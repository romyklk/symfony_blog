<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title',TextType::class,[
                'label' => false,
                'attr' =>[
                    'placeholder' => 'Titre de l\'article',
                    'class' => 'form-control mb-3'
                ],
                'required' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir un titre',
                    ]),
                    new Length([
                        'min' => 5,
                        'minMessage' => 'Le titre doit faire au moins {{ limit }} caractères',
                        // max length allowed by Symfony for security reasons
                        'max' => 255,
                    ]),
                ],
            ])
            ->add('content',TextareaType::class,[
                'label' => false,
                'attr' =>[
                    'placeholder' => 'Contenu de l\'article',
                    'class' => 'form-control mb-3',
                    'rows' => 10,
                    'cols' => 10,
                   // 'id' => 'article_content'
                ],
                'required' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir un contenu',
                    ]),
                    new Length([
                        'min' => 100,
                        'minMessage' => 'Le contenu doit faire au moins {{ limit }} caractères',
                        // max length allowed by Symfony for security reasons
                        'max' => 10000,
                    ]),
                ],
            ])
            ->add('imageFile',FileType::class,[
                'label' => false,
                'required' => false,
                'attr' =>[
                   // 'placeholder' => 'Image de l\'article',
                    'class' => 'form-control mb-3 mt-3'
                ]
            ])
            ->add('categories',EntityType::class,[
                'class' => Category::class,
                'choice_label' => 'name',
                'multiple' => true,
                'by_reference' => false, // Important pour que les catégories soient bien enregistrées dans la table article_category et non dans la table category 
                'label' => 'CHOISIR UNE OU PLUSIEURS CATEGORIES',
                'attr' =>[
                  //  'placeholder' => 'Catégories de l\'article',
                      'class' => 'form-control mb-5 choice_select'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
