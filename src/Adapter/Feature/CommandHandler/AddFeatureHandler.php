<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Feature\CommandHandler;

use Feature;
use PrestaShop\PrestaShop\Adapter\Domain\AbstractObjectModelHandler;
use PrestaShop\PrestaShop\Core\Domain\Feature\Exception\CannotAddFeatureException;
use PrestaShop\PrestaShop\Core\Domain\Feature\Exception\FeatureConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Feature\ValueObject\FeatureId;
use PrestaShop\PrestaShop\Core\Domain\Feature\Command\AddFeatureCommand;
use PrestaShop\PrestaShop\Core\Domain\Feature\CommandHandler\AddFeatureHandlerInterface;

/**
 * Handles adding of features using legacy logic.
 */
final class AddFeatureHandler extends AbstractObjectModelHandler implements AddFeatureHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(AddFeatureCommand $command)
    {
        $feature = new Feature();

        $feature->name = $command->getLocalizedNames();

        if (false === $feature->validateFields(false)) {
            throw new FeatureConstraintException('Invalid feature data');
        }

        if (false === $feature->validateFieldsLang(false)) {
            throw new FeatureConstraintException('Invalid feature data', FeatureConstraintException::EMPTY_NAME);
        }

        if (false === $feature->add()) {
            throw new CannotAddFeatureException('Unable to create new feature');
        }

        $this->associateWithShops($feature, $command->getShopAssociation());

        return new FeatureId($feature->id);
    }
}
