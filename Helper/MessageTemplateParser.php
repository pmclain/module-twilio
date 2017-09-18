<?php
/**
 * Pmclain_Twilio extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the GPL v3 License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://www.gnu.org/licenses/gpl.txt
 *
 * @category       Pmclain
 * @package        Twilio
 * @copyright      Copyright (c) 2017
 * @license        https://www.gnu.org/licenses/gpl.txt GPL v3 License
 */

namespace Pmclain\Twilio\Helper;

class MessageTemplateParser
{
    /**
     * @var array
     */
    protected $vars;

    /**
     * @var string
     */
    protected $template;

    /**
     * @param string $template
     * @param array $vars
     * @return string
     */
    public function parseTemplate($template, $vars = [])
    {
        $this->template = $template;
        $this->vars = $vars;

        if (preg_match_all('/({{[^{}]*}})/', $template, $matches) > 0) {
            $matches = $this->trimVariableMatches($matches[0]);

            foreach ($matches as $match) {
                $this->template = $this->replacePlaceholderWithVariable($match);
            }
        }

        return $this->template;
    }

    protected function trimVariableMatches($matches)
    {
        $result = [];

        foreach ($matches as $match) {
            $result[] = substr($match, 2, (strlen($match) - 4));
        }

        return $result;
    }

    protected function replacePlaceholderWithVariable($placeholder)
    {
        if (!array_key_exists($placeholder, $this->vars)) {
            return str_replace('{{' . $placeholder . '}}', '', $this->template);
        }
        if (!is_array($this->vars[$placeholder])) {
            return str_replace(
                '{{' . $placeholder . '}}',
                $this->vars[$placeholder],
                $this->template
            );
        }
        if (is_array($this->vars[$placeholder])) {
            return str_replace(
                '{{' . $placeholder . '}}',
                implode(', ', $this->vars[$placeholder]),
                $this->template
            );
        }

        return $this->template;
    }
}
