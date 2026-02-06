<?php

declare(strict_types=1);

namespace ThreeBRS\SyliusPplParcelshopsPlugin\Form\Extension;

use Sylius\Bundle\AdminBundle\Form\Type\ShippingMethodType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Contracts\Translation\TranslatorInterface;

class AdminPplShippingMethodExtension extends AbstractTypeExtension
{
    /**
     * @param array<string> $countryChoices
     */
    public function __construct(
        private readonly array $countryChoices,
        private readonly TranslatorInterface $translator,
    ) {
    }

    /** @return array<int, string> */
    public static function getExtendedTypes(): array
    {
        return [
            ShippingMethodType::class,
        ];
    }

    /** @param array<mixed> $options */
    public function buildForm(
        FormBuilderInterface $builder,
        array $options,
    ): void {
        $formCountryChoices = \array_combine($this->countryChoices, $this->countryChoices);

        $builder
            /** @see \ThreeBRS\SyliusPplParcelshopsPlugin\Model\PplShippingMethodTrait::$pplParcelshopsShippingMethod */
            ->add('pplParcelshopsShippingMethod', CheckboxType::class, [
                'label' => 'threebrs.admin.pplParcelShop.form.pplParcelshopsShippingMethod',
                'required' => false,
            ])
            /** @see \ThreeBRS\SyliusPplParcelshopsPlugin\Model\PplShippingMethodTrait::$pplDefaultCountry */
            ->add('pplDefaultCountry', ChoiceType::class, [
                'label' => 'threebrs.admin.pplParcelShop.form.pplDefaultCountry',
                'required' => false,
                'choices' => $formCountryChoices,
                'multiple' => false,
                'expanded' => false,
            ])
            /** @see \ThreeBRS\SyliusPplParcelshopsPlugin\Model\PplShippingMethodTrait::$pplOptionCountries */
            ->add('pplOptionCountries', ChoiceType::class, [
                'label' => 'threebrs.admin.pplParcelShop.form.pplOptionCountries',
                'required' => false,
                'choices' => $formCountryChoices,
                'multiple' => true,
                'expanded' => false,
            ]);

        // Add validation callback
        $builder->addEventListener(FormEvents::POST_SUBMIT, function (
            FormEvent $event,
        ) {
            $form = $event->getForm();

            $defaultCountry = $form->get('pplDefaultCountry')->getData();
            if (!$defaultCountry) {
                return;
            }
            $optionCountries = $form->get('pplOptionCountries')->getData();
            if (!is_array($optionCountries)) {
                return;
            }

            if (!in_array($defaultCountry, $optionCountries, true)) {
                $form->get('pplDefaultCountry')->addError(
                    new FormError($this->translator->trans(
                        id: 'threebrs.admin.shippingMethod.defaultCountryInNotAllowed',
                        domain: 'validators',
                    )),
                );
            }
        });
    }
}
