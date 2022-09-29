<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;

/* rendimiento.html */
class __TwigTemplate_7a7df0ea5141a37c3c17b05535ced64e extends Template
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
        echo "<!DOCTYPE html>
<html lang=\"es\">
<head>
\t<meta charset=\"UTF-8\">
\t<meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">
\t<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
\t<!-- scripts y estilos externos -->
\t<script src=\"https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.min.js\" integrity=\"sha384-ODmDIVzN+pFdexxHEHFBQH3/9/vQ9uori45z4JjnFsRydbmQbmL5t1tQ0culUzyK\" crossorigin=\"anonymous\"></script>
\t<link rel=\"stylesheet\" href=\"https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css\" integrity=\"sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx\" crossorigin=\"anonymous\">
\t<link rel=\"preconnect\" href=\"https://fonts.googleapis.com\">
    <link rel=\"preconnect\" href=\"https://fonts.gstatic.com\" crossorigin>
    <link href=\"https://fonts.googleapis.com/css2?family=Nunito&family=Roboto&display=swap\" rel=\"stylesheet\">
\t
\t<!-- scripts  y estilos propios -->
\t<script src=\"scripts/fns.js\"></script>
\t<script src=\"scripts/bdates.js\"></script>
\t<script src=\"scripts/btables.js\"></script>
\t<script src=\"scripts/rendimiento.js\" defer></script>
\t<link rel=\"stylesheet\" href=\"styles/styles.css\">
\t<link rel=\"stylesheet\" href=\"styles/btables.css\">
\t
\t<title>Document</title>
</head>
<body>

    aaaaaaaaaaaaaaaaaaaaaaaa
    <nav>
        <p>";
        // line 28
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["usuario"] ?? null), "nombre", [], "any", false, false, false, 28), "html", null, true);
        echo "</p>
    </nav>

\t<a href=\"server/services/logout.php\"><button>Cerrar sesión</button></a>
\t<main class=\"container\" onload=\"init_data()\">
\t\t<form id=\"filtro\" class=\"row\" onsubmit=\"return false\">
\t\t\t<div class=\"col-12\" action=\"\">
\t\t\t\t<div class=\"row g-3\">
\t\t\t\t\t<div class=\"col-md-6\">
\t\t\t\t\t\t<label for=\"\" class=\"form-label\">Zona:</label>
\t\t\t\t\t\t<select id=\"input_zona\" name=\"input_zona\" class=\"form-control\" onchange=\"ev_change_zona()\">
\t\t\t\t\t\t</select>
\t\t\t\t\t</div>
\t\t\t\t\t<div class=\"col-md-6\">
\t\t\t\t\t\t<label class=\"form-label\">Máquina:</label>
\t\t\t\t\t\t<select id=\"input_maquina\" name=\"input_maquina\" class=\"form-control\">
\t\t\t\t\t\t</select>
\t\t\t\t\t</div>

\t\t\t\t\t<div class=\"col-md-6\">
\t\t\t\t\t\t<label class=\"form-label\" for=\"start\">Inicio:</label>
\t\t\t\t\t\t<input type=\"date\" id=\"input_start_date\" name=\"input_start_date\" class=\"form-control\"
\t\t\t\t\t\t\tvalue=\"2022-08-01\"
\t\t\t\t\t\t\tmin=\"2012-01-01\">
\t\t\t\t\t</div>
\t\t\t\t\t<div class=\"col-md-6\">
\t\t\t\t\t\t<label class=\"form-label\" for=\"start\">Final:</label>
\t\t\t\t\t\t<input type=\"date\" id=\"input_end_date\" name=\"input_end_date\" class=\"form-control\"
\t\t\t\t\t\t\tmin=\"2012-01-01\">
\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t\t<div class=\"row justify-content-center mt-4\">
\t\t\t\t\t<button class=\"col-md-2\" onclick=\"ev_click_filter()\">Filtrar</button>
\t\t\t\t</div>\t\t\t
\t\t\t</div>
\t\t</form>\t\t
\t
\t\t<section id=\"resultados\">
\t\t\t<table id=\"tabla_resultados\">
\t\t\t</table>
\t\t</section>
\t</main>
</body>
</html>";
    }

    public function getTemplateName()
    {
        return "rendimiento.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  66 => 28,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "rendimiento.html", "C:\\xampp\\htdocs\\p1\\templates\\rendimiento.html");
    }
}
