<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\CoreExtension;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;

/* parts/header.view.html */
class __TwigTemplate_ba23e1211b8a6e61542ec6ad97574775 extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        yield "<header>
        <h1><a href=\"/\">Sistema Para Procesar Archivos de Excel</a></h1>
        <nav class=\"nav-procesador\">
                <ul>
                        <li class=\"item-list\">
                                <a href=\"/\">Procesar Archivos Excel</a>
                        </li>
                        <li class=\"item-list\">
                                <a href=\"/revisar-padron\">Revisar Padron</a>
                        </li>
                </ul>
        </nav>
</header>";
        return; yield '';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "parts/header.view.html";
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo()
    {
        return array ();
    }

    public function getSourceContext()
    {
        return new Source("<header>
        <h1><a href=\"/\">Sistema Para Procesar Archivos de Excel</a></h1>
        <nav class=\"nav-procesador\">
                <ul>
                        <li class=\"item-list\">
                                <a href=\"/\">Procesar Archivos Excel</a>
                        </li>
                        <li class=\"item-list\">
                                <a href=\"/revisar-padron\">Revisar Padron</a>
                        </li>
                </ul>
        </nav>
</header>", "parts/header.view.html", "C:\\Users\\administrator\\Documents\\GitHub\\procesamiento-excels\\src\\App\\views\\parts\\header.view.html");
    }
}
