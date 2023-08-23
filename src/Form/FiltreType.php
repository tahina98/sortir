<?php

namespace App\Form;

use App\Entity\Filtre;
use App\Entity\Site;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FiltreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('site', EntityType::class, [
                'label' => false,
                'class' => Site::class,
                'choice_label' => 'nom',
                'expanded'  => true,
                'required' => false

                //TODO mettre en liste déroulante
            ])
            ->add('nom')
            ->add('dateHeureDebut')
            ->add('dateHeureFin')
            ->add('organisateur', CheckboxType::class, [
                'mapped' => false,
                'required' => false
            ])
            ->add('inscrit', CheckboxType::class, [
                'mapped' => false,
                'required' => false
            ])

            ->add('datePassee', CheckboxType::class, [
                'mapped' => false,
                'required' => false
            ])
            ->add('submit',SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Filtre::class,
            'method' => 'GET',
            'csrf_protection' => false
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}
