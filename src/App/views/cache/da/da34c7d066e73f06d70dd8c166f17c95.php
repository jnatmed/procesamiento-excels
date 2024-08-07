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

/* parts/head.view.html */
class __TwigTemplate_6982c7893fa90436c0248465bd359ceb extends Template
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
        // line 1
        yield "<meta charset=\"UTF-8\">
<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
<link rel=\"stylesheet\" href=\"/assets/css/base.css\">
<link rel=\"stylesheet\" href=\"/assets/css/not-found.css\">

<script src=\"/assets/js/app.js\"></script>

<title>";
        // line 8
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((array_key_exists("titulo", $context)) ? (Twig\Extension\CoreExtension::default(($context["titulo"] ?? null), "Procesador de Excels")) : ("Procesador de Excels")), "html", null, true);
        yield "</title>
";
        return; yield '';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "parts/head.view.html";
    }

    /**
     * @codeCoverageIgnore
     */
    public function isTraitable()
    {
        return false;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo()
    {
        return array (  47 => 8,  38 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("<meta charset=\"UTF-8\">
<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
<link rel=\"stylesheet\" href=\"/assets/css/base.css\">
<link rel=\"stylesheet\" href=\"/assets/css/not-found.css\">

<script src=\"/assets/js/app.js\"></script>

<title>{{ titulo | default('Procesador de Excels') }}</title>
", "parts/head.view.html", "C:\\Users\\administrator\\Documents\\GitHub\\procesamiento-excels\\src\\App\\views\\parts\\head.view.html");
    }
}
