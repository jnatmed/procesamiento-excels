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

/* index.view.html */
class __TwigTemplate_18bee2095271536796b0160673e2d41c extends Template
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
        yield "<!DOCTYPE html>
<html lang=\"es\">
<head>
    ";
        // line 4
        yield from         $this->loadTemplate("parts/head.view.html", "index.view.html", 4)->unwrap()->yield($context);
        // line 5
        yield "</head>
<body class=\"home\">
    ";
        // line 7
        yield from         $this->loadTemplate("parts/header.view.html", "index.view.html", 7)->unwrap()->yield($context);
        // line 8
        yield "
    <section>
        <h3>Comparar Archivos Excel</h3>
        <form id=\"upload-form\" action=\"/excel/procesar\" method=\"POST\" enctype=\"multipart/form-data\">
            <article>
                <h3>Primer Excel</h3>
                <div class=\"drop-zone\" id=\"drop-zone-1\">Arrastra y suelta tu archivo aquí o haz click para subir</div>
                <input type=\"file\" id=\"file-input-1\" name=\"excel-file-1\" accept=\".xlsx\">
            </article>
            <article>
                <h3>Segundo Excel</h3>
                <div class=\"drop-zone\" id=\"drop-zone-2\">Arrastra y suelta tu archivo aquí o haz click para subir</div>
                <input type=\"file\" id=\"file-input-2\" name=\"excel-file-2\" accept=\".xlsx\">
            </article>
            <button type=\"submit\" id=\"compare-button\">Comparar Archivos</button>
        </form>
    </section>

    <div id=\"table-container-1\" class=\"table-container\"></div>
    <div id=\"table-container-2\" class=\"table-container\"></div>
    <div id=\"table-container-3\" class=\"table-container\"></div>

</body>
</html>
";
        return; yield '';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "index.view.html";
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
        return array (  51 => 8,  49 => 7,  45 => 5,  43 => 4,  38 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("<!DOCTYPE html>
<html lang=\"es\">
<head>
    {% include 'parts/head.view.html' %}
</head>
<body class=\"home\">
    {% include 'parts/header.view.html' %}

    <section>
        <h3>Comparar Archivos Excel</h3>
        <form id=\"upload-form\" action=\"/excel/procesar\" method=\"POST\" enctype=\"multipart/form-data\">
            <article>
                <h3>Primer Excel</h3>
                <div class=\"drop-zone\" id=\"drop-zone-1\">Arrastra y suelta tu archivo aquí o haz click para subir</div>
                <input type=\"file\" id=\"file-input-1\" name=\"excel-file-1\" accept=\".xlsx\">
            </article>
            <article>
                <h3>Segundo Excel</h3>
                <div class=\"drop-zone\" id=\"drop-zone-2\">Arrastra y suelta tu archivo aquí o haz click para subir</div>
                <input type=\"file\" id=\"file-input-2\" name=\"excel-file-2\" accept=\".xlsx\">
            </article>
            <button type=\"submit\" id=\"compare-button\">Comparar Archivos</button>
        </form>
    </section>

    <div id=\"table-container-1\" class=\"table-container\"></div>
    <div id=\"table-container-2\" class=\"table-container\"></div>
    <div id=\"table-container-3\" class=\"table-container\"></div>

</body>
</html>
", "index.view.html", "C:\\Users\\administrator\\Documents\\GitHub\\procesamiento-excels\\src\\App\\views\\index.view.html");
    }
}
