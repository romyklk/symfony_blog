<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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

                ]
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
                'required' => false
            ])
            ->add('imageFile',FileType::class,[
                'label' => false,
                'attr' =>[
                   // 'placeholder' => 'Image de l\'article',
                    'class' => 'form-control mb-3 mt-3'
                ]
            ])
            ->add('categories',EntityType::class,[
                'class' => Category::class,
                'choice_label' => 'name',
                'multiple' => true,
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
