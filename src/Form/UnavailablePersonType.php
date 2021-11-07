<?php

namespace App\Form;

use App\Entity\Person;
use App\Entity\Planning;
use App\Entity\UnavailablePerson;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UnavailablePersonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var Planning */
        $planning = $options['planning'];

        $gameChoices = [];
        for ($game = 0 ; $game < $planning->getGameCount() ; ++$game) {
            $gameChoices[$game + 1] = $game;
        }


        $builder
            ->add('game', ChoiceType::class, [
                'choices' => $gameChoices
            ])
            ->add('person', EntityType::class, [
                'class' => Person::class,
                'choices' => $planning->getPersons()
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UnavailablePerson::class,
            'planning' => null
        ]);
    }
}
