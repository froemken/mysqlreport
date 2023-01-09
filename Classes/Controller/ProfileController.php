<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Controller;

use Psr\Http\Message\ResponseInterface;
use StefanFroemken\Mysqlreport\Domain\Repository\ProfileRepository;

/**
 * Controller to show and analyze all queries of a request
 */
class ProfileController extends AbstractController
{
    protected ProfileRepository $profileRepository;

    public function injectProfileRepository(ProfileRepository $profileRepository): void
    {
        $this->profileRepository = $profileRepository;
    }

    public function listAction(): ResponseInterface
    {
        $this->view->assign('profileRecords', $this->profileRepository->findProfileRecordsForCall());

        $moduleTemplate = $this->getModuleTemplate();
        $moduleTemplate->setContent($this->view->render());

        return $this->htmlResponse($moduleTemplate->renderContent());
    }

    public function showAction(string $uniqueIdentifier): ResponseInterface
    {
        $this->view->assign('profileTypes', $this->profileRepository->getProfileRecordsByUniqueIdentifier($uniqueIdentifier));

        $moduleTemplate = $this->getModuleTemplate();
        $moduleTemplate->setContent($this->view->render());

        return $this->htmlResponse($moduleTemplate->renderContent());
    }

    public function queryTypeAction(string $uniqueIdentifier, string $queryType): ResponseInterface
    {
        $this->view->assign('uniqueIdentifier', $uniqueIdentifier);
        $this->view->assign('queryType', $queryType);
        $this->view->assign('profileRecords', $this->profileRepository->getProfileRecordsByQueryType($uniqueIdentifier, $queryType));

        $moduleTemplate = $this->getModuleTemplate();
        $moduleTemplate->setContent($this->view->render());

        return $this->htmlResponse($moduleTemplate->renderContent());
    }

    public function profileInfoAction(int $uid): ResponseInterface
    {
        $profileRecord = $this->profileRepository->getProfileRecordByUid($uid);
        $profileRecord['profile'] = unserialize($profileRecord['profile'], ['allowed_classes' => false]);
        $profileRecord['explain'] = unserialize($profileRecord['explain_query'], ['allowed_classes' => false]);

        $this->view->assign('profileRecord', $profileRecord);

        $moduleTemplate = $this->getModuleTemplate();
        $moduleTemplate->setContent($this->view->render());

        return $this->htmlResponse($moduleTemplate->renderContent());
    }
}
