# H-Project — Codex Context Handoff

**Snapshot date:** 2026-10-10  
**Suggested destination:** `.codex/context/H-PROJECT-CONTEXT.md`

> This is a handoff from the ChatGPT session used to design and begin H-Project.
>
> **The real repository is authoritative.** Inspect the filesystem before editing. If this document conflicts with newer code, preserve intentional newer work and update your mental model from the repository.

---

# 1. Project Purpose

H-Project is a modular, locally controlled AI harness for personal use.

The user wants a home AI system suitable for large projects while retaining control over:

- harness/orchestration;
- model/runtime selection;
- memory and context construction;
- APIs;
- permissions;
- tools;
- system/peripheral access;
- persistent storage;
- service boundaries.

The project is **not** an attempt to implement an LLM or low-level inference engine from scratch. Existing inference runtimes and normal hardware/inference APIs are acceptable.

The project should own the higher-level system around inference.

---

# 2. Working Style

The user is technically competent and wants to understand the architecture while building it.

When working on this repository:

- Be concise and direct.
- Do not over-explain obvious concepts.
- Do not repeatedly reopen decisions already made.
- Let the user lead architecture brainstorming.
- Use incremental, testable changes.
- Explain PHP/HTTP/JS fundamentals precisely when asked.
- Inspect actual files before assuming their contents.
- Do not introduce frameworks or major abstractions without a concrete reason.

## Code style

Use **Allman braces** everywhere.

Correct:

```php
if ($condition)
{
    doSomething();
}
```

Correct:

```javascript
function handleRequest()
{
    // ...
}
```

Do not use:

```php
if ($condition) {
    doSomething();
}
```

Keep comments sparse. Only comment non-obvious behavior, important intent, security constraints, or unusual workarounds.

---

# 3. Platform and Hardware

Development host:

```text
Windows 11
```

Development environment:

```text
WSL2 / Linux
```

Canonical/final target:

```text
Debian Linux home server
```

Current hardware:

```text
GPU: RTX 5070 Ti, 16 GB VRAM
CPU: Ryzen 7 5700X3D
RAM: 32 GB
```

---

# 4. Development Workflow

Linux/Debian is canonical even though development currently occurs under Windows 11 + WSL2.

For C/C++ components:

```text
write source
    ↓
CMake build
    ↓
native WSL/Linux testing
    ↓
working component
    ↓
Docker build
    ↓
container testing
    ↓
tag/push to local registry
    ↓
Docker Compose integration
```

Docker is primarily the packaging/integration stage rather than the mandatory daily development environment for every component.

Container boundaries should correspond to meaningful replaceable/security/resource boundaries.

Avoid unnecessary microservices.

GPU access should initially be isolated to the inference service/container where practical.

---

# 5. Technology Decisions

## Harness

The harness is intentionally:

```text
Frontend: HTML + JavaScript
Backend: PHP + Apache
```

Do not restart a PHP-vs-C++ discussion unless requested.

## C++

C++ is intended for performance-sensitive/library-heavy components and services.

CMake is the intended build system.

## Inference

No final inference engine has been committed to.

`llama.cpp` has been discussed as a candidate.

The user has Ollama installed from older work but explicitly does **not** want Ollama to be the chosen H-Project runtime. Do not automatically integrate or recommend it.

---

# 6. High-Level Architecture

Current coarse direction:

```text
H-Project
│
├── harness
│   ├── Apache / PHP
│   ├── HTML / JS
│   ├── API / dispatch
│   ├── orchestration
│   ├── conversation/session handling
│   ├── context assembly
│   ├── permissions/config
│   └── tool/task coordination
│
├── inference
│   └── C/C++ model runtime
│
├── database
│   └── persistent state
│
├── worker
│   └── background C++ processing
│
└── sandbox
    └── later isolated code/tool execution
```

Logical modules such as context builder, prompt builder, conversation manager, and tool router can initially remain inside the harness. Split them only when there is a meaningful reason.

---

# 7. AI / Context Mental Model Already Established

The user already understands:

- model inference is generally stateless per request;
- context is everything supplied to the model, not just chat history;
- context window, conversation history, and long-term memory are different things;
- the database can contain much more information than is sent to a model;
- the harness retrieves relevant information and assembles model context;
- context can be assembled linearly from non-linear storage;
- structured records/JSON-like data are appropriate internally;
- long-term memory should be separate from raw transcript.

Simplified future request path:

```text
user message
    ↓
persist message
    ↓
retrieve relevant information
    ↓
assemble context
    ↓
call inference
    ↓
receive output/tool requests
    ↓
persist/process
    ↓
return response
```

---

# 8. Future Context Processor Direction

A smaller model may later act as a context compiler:

```text
deterministic retrieval
        ↓
candidate context
        ↓
small context model
        ↓
dedupe / summarize / rank / extract
        ↓
structured prepared context
        ↓
main model
```

Conceptual interface discussed:

```text
ContextProcessor.process(
    current_prompt,
    candidate_context,
    token_budget
)
→ PreparedContext
```

Principles:

- deterministic retrieval first;
- original records remain source of truth;
- context model should preferably return strict structured output;
- harness/context builder remains authoritative;
- later the main model may request additional context on demand.

A ~3B-class model was discussed as potentially sufficient, but nothing has been committed to.

---

# 9. Repository Location

Project root:

```text
~/Projects/H-Project
```

Current active area:

```text
~/Projects/H-Project/harness
```

Agent/context directories were created/planned:

```text
~/Projects/H-Project/.codex/context
~/Projects/H-Project/.codex/docs
~/Projects/H-Project/.codex/notes
```

This handoff file should live at:

```text
~/Projects/H-Project/.codex/context/H-PROJECT-CONTEXT.md
```

A future root-level `AGENTS.md` can contain short persistent coding/architecture instructions.

---

# 10. Current Harness Structure

Approximate current structure:

```text
harness/
├── public/
│   ├── index.php
│   ├── css/
│   │   └── index.css
│   ├── js/
│   │   └── index.js
│   ├── php/
│   └── .htaccess.disabled
│
├── src/
│   ├── function_map.php
│   └── functions/
│       └── process_prompt.php
│
├── router/
│   └── router.php
│
└── apache/
```

Inspect the actual filesystem. Some directories may be empty and files may have evolved.

---

# 11. Apache / PHP Development Setup

Apache and PHP were installed natively inside WSL for direct development/testing.

Packages included approximately:

```bash
sudo apt update
sudo apt install -y \
    apache2 \
    php \
    libapache2-mod-php \
    php-cli \
    php-curl \
    php-json \
    php-mbstring \
    php-xml \
    php-sqlite3
```

Apache was enabled with:

```bash
sudo systemctl enable --now apache2
```

Apache `DocumentRoot` points at the project's:

```text
~/Projects/H-Project/harness/public
```

rather than `/var/www/html`.

Conceptual site config:

```apache
<VirtualHost *:80>
    DocumentRoot "/actual/path/to/H-Project/harness/public"

    <Directory "/actual/path/to/H-Project/harness/public">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/h-project-error.log
    CustomLog ${APACHE_LOG_DIR}/h-project-access.log combined
</VirtualHost>
```

Relevant commands:

```bash
sudo a2dissite 000-default.conf
sudo a2ensite h-project.conf
sudo apache2ctl configtest
sudo systemctl reload apache2
```

---

# 12. Apache Permission Issue Already Solved

The project initially produced:

```text
403 Forbidden
```

Apache logs showed filesystem permission/traversal failure under the user's home directory.

Diagnostics included:

```bash
namei -l "$(realpath public/index.php)"
sudo tail -n 20 /var/log/apache2/h-project-error.log
```

Traversal/read permissions were adjusted approximately with:

```bash
chmod o+x ~
chmod o+x ~/Projects
chmod o+x ~/Projects/H-Project
chmod o+x ~/Projects/H-Project/harness
chmod o+x ~/Projects/H-Project/harness/public
chmod -R o+r public
```

This allowed Apache to reach the application.

Do not repeat this work unless the current system indicates a regression.

---

# 13. Rewrite / `.htaccess` State

`mod_rewrite` was enabled:

```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

An `.htaccess` was created:

```apache
RewriteEngine On

RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d

RewriteRule ^ index.php [QSA,L]
```

During debugging it was renamed to:

```text
public/.htaccess.disabled
```

The HTTP 500 persisted with rewrite disabled, so `.htaccess` was not the cause of that failure.

Do not assume rewrite has since been re-enabled.

Also: do not become overly focused on routing the old `/api/status` toy endpoint. The user explicitly said that was being overemphasized. It was only an early routing test and has no architectural importance.

---

# 14. PHP `strict_types` Debugging

VS Code reported:

```text
strict_types declaration must be the very first statement in the script
```

The visible source was valid:

```php
<?php
declare(strict_types=1);

$pageTitle = 'H-Project';
```

An invisible UTF-8 BOM was therefore suspected.

Diagnostic:

```bash
xxd -l 8 public/index.php
```

Normal beginning:

```text
3c3f 7068 70
```

BOM beginning:

```text
efbb bf3c 3f70 6870
```

Potential fix:

```bash
sed -i '1s/^\xEF\xBB\xBF//' public/index.php
```

Syntax test:

```bash
php -l public/index.php
```

The user subsequently declared this setup milestone complete.

Do not assume this remains an active problem.

---

# 15. Last Confirmed `public/index.php`

The user supplied:

```php
<?php
declare(strict_types=1);


$pageTitle = 'H-Project';

?>

<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= htmlspecialchars($pageTitle) ?></title>

    <link rel="stylesheet" href="/css/index.css">
</head>

<body>
    <header>
        <h1><?= htmlspecialchars($pageTitle) ?></h1>
    </header>

    <main>
        <section id="chat">
            <div id="messages">
            </div>

            <form id="prompt-form">
                <textarea
                    id="prompt"
                    name="prompt"
                    placeholder="Enter a message..."
                    rows="4"
                ></textarea>

                <button type="submit">Send</button>
            </form>
        </section>
    </main>

    <script src="/js/index.js"></script>
</body>
</html>
```

Using PHP rather than static HTML is intentional so server-side setup/preprocessing can be added when needed.

---

# 16. Earlier Router State

An earlier `router/router.php` looked approximately like:

```php
<?php

declare(strict_types=1);

$routes =
[
    'GET' =>
    [
        '/'           => __DIR__ . '/../public/index.html',
        '/api/status' => __DIR__ . '/../src/status.php'
    ],

    'POST' =>
    [
        // '/api/chat' => __DIR__ . '/../src/chat.php'
    ]
];

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if (!isset($routes[$method]))
{
    http_response_code(405);
    header('Content-Type: application/json');

    echo json_encode(
    [
        'error' => 'Method not allowed'
    ]);

    exit;
}

if (!isset($routes[$method][$path]))
{
    http_response_code(404);
    header('Content-Type: application/json');

    echo json_encode(
    [
        'error' => 'Route not found'
    ]);

    exit;
}

$handler = $routes[$method][$path];

if (!is_file($handler))
{
    http_response_code(500);
    header('Content-Type: application/json');

    echo json_encode(
    [
        'error' => 'Route handler missing'
    ]);

    exit;
}

$extension = pathinfo($handler, PATHINFO_EXTENSION);

if ($extension === 'html')
{
    header('Content-Type: text/html; charset=utf-8');
    readfile($handler);

    exit;
}

require $handler;
```

The architecture has evolved since this.

At one point `/` was going to map to `index.php`.

Avoid recursion such as:

```text
index.php
→ router.php
→ route /
→ index.php
→ router.php
→ ...
```

Inspect current files before modifying the router.

---

# 17. PHP Concepts the User Already Understands

Do not re-teach these unless asked:

- HTTP methods and `$_SERVER['REQUEST_METHOD']`;
- `$_SERVER['REQUEST_URI']`;
- `parse_url(..., PHP_URL_PATH)`;
- PHP superglobals;
- `declare(strict_types=1)`;
- dynamic typing vs PHP type declarations;
- function evaluation before assignment;
- `isset()`;
- `http_response_code()`;
- `header()`;
- `echo`;
- HTTP status line / headers / blank line / body;
- PHP/Apache dynamically constructing responses.

---

# 18. Initial JavaScript Prompt Capture

The first proposed `public/js/index.js` was:

```javascript
"use strict";

const promptForm = document.getElementById("prompt-form");
const promptInput = document.getElementById("prompt");
const messages = document.getElementById("messages");

promptForm.addEventListener("submit", handlePromptSubmit);

function handlePromptSubmit(event)
{
    event.preventDefault();

    const userText = promptInput.value.trim();

    if (userText === "")
    {
        return;
    }

    console.log(userText);
}
```

Purpose:

```text
form submission
    ↓
JavaScript handler
    ↓
capture prompt
```

The user then paused before implementing `fetch()` because they wanted to design the PHP backend dispatch mechanism first.

Inspect `public/js/index.js` to see its actual current state.

---

# 19. Current Backend Dispatch Direction

This is the **active design point** at handoff.

The user proposed:

```text
JavaScript captures user prompt
    ↓
POST /function-map/(function-name)
    ↓
PHP controlled backend access
    ↓
mapped backend function
```

Example:

```text
/function-map/process-prompt
```

Conceptually:

```text
index.js
    │
    │ POST /function-map/process-prompt
    ▼
generic PHP dispatcher
    │
    ▼
function map
    │
    │ "process-prompt"
    ▼
processPrompt(...)
    │
    ├── validation
    ├── conversation handling
    ├── context construction
    ├── inference later
    └── result construction
```

The browser must **not** be allowed to specify arbitrary PHP files or arbitrary internal functions.

The function map is intended as an explicit allow-list/registry of externally callable operations.

The exact public URL naming is not permanently fixed. `/function-map/...` is the user's current conceptual naming. A conventional `/api/...` namespace was mentioned but should not be forced on the user.

---

# 20. `src/function_map.php`

The user was given this command/content:

```php
<?php

declare(strict_types=1);

$functionMap =
[
    'process-prompt' => __DIR__ . '/functions/process_prompt.php'
];
```

Expected location:

```text
harness/src/function_map.php
```

Future illustrative map entries might include:

```text
process-prompt
get-conversation
create-conversation
get-models
get-settings
```

Do not create these merely because they appear here.

---

# 21. `src/functions/process_prompt.php`

The initial backend function was:

```php
<?php

declare(strict_types=1);

function processPrompt(string $prompt): array
{
    return
    [
        'prompt' => $prompt,
        'response' => 'Backend received prompt.'
    ];
}
```

Expected location:

```text
harness/src/functions/process_prompt.php
```

Important distinction established with the user:

`processPrompt()` is **not** the generic HTTP handler.

It is application/backend logic.

Its current contract is roughly:

```text
input:
    string $prompt

output:
    PHP array defined by processPrompt()
```

The array structure is application-specific. PHP does not require this exact response shape.

The generic HTTP dispatcher should not need to understand the internal structure of the returned array. It should invoke the allowed backend operation, receive its result, serialize the result, and return it over HTTP.

---

# 22. Native PHP Internals vs JSON Boundary

A deliberate design principle was established:

> Use native PHP values inside the PHP harness. Use JSON when crossing an HTTP/service boundary.

Example:

```text
JavaScript object
    ↓
JSON.stringify()
    ↓
HTTP request body
    ↓
PHP dispatcher
    ↓
json_decode(...)
    ↓
native PHP values
    ↓
processPrompt(...)
    ↓
PHP array
    ↓
generic dispatcher
    ↓
json_encode(...)
    ↓
HTTP response
    ↓
response.json()
    ↓
JavaScript object
```

Do not make every internal PHP function manually encode/decode JSON.

---

# 23. Planned JavaScript Request Contract

The next JS evolution discussed was approximately:

```javascript
"use strict";

const promptForm = document.getElementById("prompt-form");
const promptInput = document.getElementById("prompt");
const messages = document.getElementById("messages");

promptForm.addEventListener("submit", handlePromptSubmit);

async function handlePromptSubmit(event)
{
    event.preventDefault();

    const userText = promptInput.value.trim();

    if (userText === "")
    {
        return;
    }

    const response = await fetch("/function-map/process-prompt",
    {
        method: "POST",

        headers:
        {
            "Content-Type": "application/json"
        },

        body: JSON.stringify(
        {
            prompt: userText
        })
    });

    const result = await response.json();

    console.log(result);
}
```

This was **discussed but should not be assumed to have been written to disk**.

The user wanted to understand/design the backend function access layer before proceeding.

---

# 24. Generic Dispatcher — Missing Piece

At the exact handoff point, the main missing backend piece is the **generic dispatcher**.

Its conceptual responsibility:

```text
HTTP request
    ↓
determine requested exposed operation
    ↓
load/check function map
    ↓
reject unregistered operation
    ↓
decode JSON request body
    ↓
load mapped backend implementation
    ↓
call appropriate backend function
    ↓
receive native PHP result
    ↓
JSON encode result
    ↓
send HTTP response
```

The design needs to decide cleanly how the map stores both:

- implementation file;
- callable/function identity.

The current map only points at a PHP file:

```php
'process-prompt' => __DIR__ . '/functions/process_prompt.php'
```

That alone does not generically tell the dispatcher which PHP callable to invoke unless a convention or richer map entry is introduced.

This is a good point for the next design discussion.

Possible direction, **not yet approved**, would be something structurally like:

```php
$functionMap =
[
    'process-prompt' =>
    [
        'file' => __DIR__ . '/functions/process_prompt.php',
        'callable' => 'processPrompt'
    ]
];
```

Do not silently implement this as final architecture without discussing it with the user.

---

# 25. Request/Response Mental Model for Current Work

The current target pipeline is:

```text
User
    ↓
index.php form
    ↓
index.js
    ↓
capture prompt string
    ↓
JSON HTTP POST
    ↓
PHP dispatcher
    ↓
function map
    ↓
processPrompt()
    ↓
PHP array
    ↓
dispatcher json_encode()
    ↓
HTTP JSON response
    ↓
index.js response.json()
    ↓
render result
```

Initially the backend response can remain a simple test response.

Do not connect an LLM yet merely to prove this pipeline.

---

# 26. Milestones

## Milestone 1 — Completed

**Harness web foundation**

Completed/established:

- WSL/Linux development environment;
- Apache/PHP native development setup;
- project DocumentRoot;
- filesystem access issue diagnosed;
- initial harness directory layout;
- PHP index page;
- CSS/JS linking;
- initial router work;
- basic understanding of HTTP/PHP flow;
- basic JS prompt capture.

The user explicitly declared this milestone complete.

## Milestone 2 — Current

**Frontend ↔ backend function dispatch**

Goal:

```text
user prompt
→ JS
→ HTTP
→ controlled PHP dispatcher
→ backend function
→ JSON response
→ JS
```

The current work is at the boundary between defining the function map and implementing the generic dispatcher / JS `fetch()`.

---

# 27. Later Milestones / Direction

A rough future progression discussed:

```text
Milestone 1
web/PHP foundation
    ✓

Milestone 2
working frontend ↔ backend dispatch/API
    ↓

Milestone 3
conversation/session representation
    ↓

Milestone 4
model-service API contract
    ↓

C++ inference service
    ↓

main model responses
    ↓

persistent conversation/context management
    ↓

context processor / smaller model
    ↓

workers / tools / sandbox
```

This is directional rather than immutable.

---

# 28. Important Design Principles to Preserve

1. **Local control first.**

2. **Explicit permissions and service boundaries.**

3. **Browser/API callers do not gain arbitrary backend function/file execution.**

4. **Function map acts as an allow-list, not dynamic arbitrary dispatch.**

5. **Native PHP values internally; JSON at API/service boundaries.**

6. **Inference is a replaceable service, not hard-wired into the web layer.**

7. **Do not over-microservice the initial implementation.**

8. **Develop components natively first, containerize once working.**

9. **Debian/Linux behavior is canonical.**

10. **Use C++ where performance/library requirements justify it, not everywhere.**

11. **Keep context/memory orchestration under harness control.**

12. **Original stored data remains authoritative even if a small model later summarizes/contextualizes it.**

13. **Do not expose backend internals simply because they exist as PHP functions.**

14. **Prefer observable, incremental milestones over large opaque implementations.**

---

# 29. Immediate Codex Instructions

Before doing anything:

1. Inspect:

```text
harness/public/index.php
harness/public/js/index.js
harness/src/function_map.php
harness/src/functions/process_prompt.php
harness/router/router.php
```

2. Inspect the Apache/rewrite state if relevant.

3. Treat actual files as authoritative.

4. Do not rewrite the project into a framework.

5. Preserve Allman braces and sparse comments.

6. Do not integrate an inference runtime yet.

7. Continue from the current goal: establish a controlled frontend → PHP backend function dispatch path.

8. Discuss meaningful architectural choices with the user rather than silently deciding them.

---

# 30. Exact Handoff Point

The last question before this handoff was effectively:

> We have a JS prompt, a PHP function map, and a `processPrompt()` backend function. What should `index.js` and the generic PHP dispatch layer do so JavaScript can safely call the exposed backend operation?

The immediate technical gap is:

```text
POST /function-map/process-prompt
            ↓
        ??? dispatcher
            ↓
      function_map.php
            ↓
       processPrompt()
```

Design and implement that gap incrementally with the user.

Do not skip directly to model inference.

---

# End of Snapshot
