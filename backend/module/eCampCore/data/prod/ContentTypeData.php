<?php

namespace eCamp\CoreData;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;
use eCamp\ContentType\Material\Strategy as MaterialStrategy;
use eCamp\ContentType\SingleText\Strategy as SingleTextStrategy;
use eCamp\ContentType\Storyboard\Strategy as StoryboardStrategy;
use eCamp\Core\Entity\ContentType;

class ContentTypeData extends AbstractFixture {
    public static $STORYBOARD = ContentType::class.':STORYBOARD';
    public static $STORYCONTEXT = ContentType::class.':STORYCONTEXT';
    public static $SAFETYCONCEPT = ContentType::class.':SAFETYCONCEPT';
    public static $NOTES = ContentType::class.':NOTES';
    public static $MATERIAL = ContentType::class.':MATERIAL';

    public function load(ObjectManager $manager) {
        $repository = $manager->getRepository(ContentType::class);

        // Story board (Programmablauf)
        $contentType = $repository->findOneBy(['name' => 'Storyboard']);
        if (null == $contentType) {
            $contentType = new ContentType();
            $contentType->setName('Storyboard');
            $contentType->setStrategyClass(StoryboardStrategy::class);
            $manager->persist($contentType);
        }
        $this->addReference(self::$STORYBOARD, $contentType);

        // Story context (Roter Faden, Einkleidung, ...)
        // implemented with a simple text field
        $contentType = $repository->findOneBy(['name' => 'StoryContext']);
        if (null == $contentType) {
            $contentType = new ContentType();
            $contentType->setName('Storycontext');
            $contentType->setStrategyClass(SingleTextStrategy::class);
            $manager->persist($contentType);
        }
        $this->addReference(self::$STORYCONTEXT, $contentType);

        // SafetyConcept
        $contentType = $repository->findOneBy(['name' => 'SafetyConcept']);
        if (null == $contentType) {
            $contentType = new ContentType();
            $contentType->setName('SafetyConcept');
            $contentType->setStrategyClass(SingleTextStrategy::class);
            $manager->persist($contentType);
        }
        $this->addReference(self::$SAFETYCONCEPT, $contentType);

        // Notes (Notizen)
        $contentType = $repository->findOneBy(['name' => 'Notes']);
        if (null == $contentType) {
            $contentType = new ContentType();
            $contentType->setName('Notes');
            $contentType->setStrategyClass(SingleTextStrategy::class);
            $manager->persist($contentType);
        }
        $this->addReference(self::$NOTES, $contentType);

        // Material
        $contentType = $repository->findOneBy(['name' => 'Material']);
        if (null == $contentType) {
            $contentType = new ContentType();
            $contentType->setName('Material');
            $contentType->setStrategyClass(MaterialStrategy::class);
            $manager->persist($contentType);
        }
        $this->addReference(self::$MATERIAL, $contentType);

        $manager->flush();
    }
}
