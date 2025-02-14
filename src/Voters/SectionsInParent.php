<?php

namespace Sven\ArtisanView\Voters;

use Illuminate\Support\Str;
use Sven\ArtisanView\Blocks\InlineSection;
use Sven\ArtisanView\Blocks\ModelForm;
use Sven\ArtisanView\Blocks\Section;
use Sven\ArtisanView\BlockStack;
use Symfony\Component\Console\Input\InputInterface;

class SectionsInParent implements Voter
{
    /**
     * @var string
     */
    protected $path;

    /**
     * {@inheritdoc}
     */
    public function canHandle(InputInterface $input)
    {
        return $input->hasOption('section');
    }

    public function inPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function run(InputInterface $input, BlockStack $blockStack)
    {
        foreach ((array) $input->getOption('section') as $section) {
            if (Str::contains($section, ':')) {
                list($name, $value) = explode(':', $section);

                ($name == 'form')
                    ? $blockStack->add(new ModelForm($name, $value))
                    : $blockStack->add(new InlineSection($name, $value));
            } else {
                $blockStack->add(new Section($section));
            }
        }
    }
}
