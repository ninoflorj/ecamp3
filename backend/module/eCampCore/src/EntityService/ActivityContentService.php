<?php

namespace eCamp\Core\EntityService;

use Doctrine\ORM\ORMException;
use eCamp\Core\ContentType\ContentTypeStrategyProvider;
use eCamp\Core\ContentType\ContentTypeStrategyProviderTrait;
use eCamp\Core\Entity\Activity;
use eCamp\Core\Entity\ActivityContent;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\ContentType;
use eCamp\Core\Hydrator\ActivityContentHydrator;
use eCamp\Lib\Acl\NoAccessException;
use eCamp\Lib\Service\ServiceUtils;
use Laminas\ApiTools\ApiProblem\ApiProblem;
use Laminas\Authentication\AuthenticationService;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class ActivityContentService extends AbstractEntityService {
    use ContentTypeStrategyProviderTrait;

    public function __construct(ServiceUtils $serviceUtils, AuthenticationService $authenticationService, ContentTypeStrategyProvider $contentTypeStrategyProvider) {
        parent::__construct(
            $serviceUtils,
            ActivityContent::class,
            ActivityContentHydrator::class,
            $authenticationService
        );

        $this->setContentTypeStrategyProvider($contentTypeStrategyProvider);
    }

    /**
     * @param mixed $data
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ORMException
     * @throws NoAccessException
     *
     * @return ActivityContent|ApiProblem
     */
    public function createEntity($data) {
        /** @var ActivityContent $activityContent */
        $activityContent = parent::createEntity($data);

        /** @var Activity $activity */
        $activity = $this->findRelatedEntity(Activity::class, $data, 'activityId');

        /** @var ContentType $contentType */
        $contentType = $this->findRelatedEntity(ContentType::class, $data, 'contentTypeId');

        $activity->addActivityContent($activityContent);
        $activityContent->setContentType($contentType);
        $activityContent->setContentTypeStrategyProvider($this->getContentTypeStrategyProvider());

        return $activityContent;
    }

    protected function fetchAllQueryBuilder($params = []) {
        $q = parent::fetchAllQueryBuilder($params);
        $q->join('row.activity', 'e');
        $q->andWhere($this->createFilter($q, Camp::class, 'e', 'camp'));

        if (isset($params['activityId'])) {
            $q->andWhere('row.activity = :activityId');
            $q->setParameter('activityId', $params['activityId']);
        }

        return $q;
    }

    protected function fetchQueryBuilder($id) {
        $q = parent::fetchQueryBuilder($id);
        $q->join('row.activity', 'e');
        $q->andWhere($this->createFilter($q, Camp::class, 'e', 'camp'));

        return $q;
    }
}
