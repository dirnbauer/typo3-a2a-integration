..  _start:

=================
A2A Integration
=================

:Extension key: a2a_integration
:Package name:  webconsulting/a2a-integration
:Version:       0.1.0
:Language:      en
:License:       GPL-2.0-or-later

The **A2A** (Agent2Agent) protocol for TYPO3 **v14** — the site publishes an
**Agent Card**, and agents discover it and delegate **tasks** over JSON-RPC. The
serving agent streams a task lifecycle (``submitted → working → input-required →
completed``) and returns its work as **artifacts**. An agent can pause mid-task
and ask the caller for one more detail (``input-required``) — collaboration, not
fire-and-forget.

Introduction
============

The extension ships two demonstrations of the A2A idea:

*   a **backend A2A Console** that plays a *client agent* — it discovers the
    site's Agent Card, delegates a task and renders the streamed lifecycle and
    the returned artifact; and
*   a **frontend "Concierge" plugin** (a Content Block) where a visitor delegates
    a task to the site's agent and watches it complete.

It belongs to a family of agent-stack demos for TYPO3 (MCP ↔ tools, A2A ↔ agents,
AG‑UI ↔ the user, A2UI ↔ the UI surface, UCP ↔ merchants, AP2 ↔ payment
authorization). A2A is the **agent ↔ agent** layer.

Why it is good
==============

For editors:
delegate real work (summarise, draft, plan) and get a usable artifact back, while
watching each lifecycle step — and the agent **asks before it assumes** when a
decision is needed.

For companies:
A2A is an open, Linux-Foundation standard. Any compliant agent can discover your
site's agent from its **Agent Card** and collaborate with it — interoperability
without a bespoke API per partner. Every task is logged for auditability.

Installation
============

..  code-block:: bash

    composer require webconsulting/a2a-integration
    vendor/bin/typo3 extension:setup
    vendor/bin/typo3 cache:flush
    vendor/bin/typo3 a2a:seed:demo --page=1068

``friendsoftypo3/content-blocks`` is required only for the frontend Concierge.
``netresearch/nr-llm`` is an optional (soft) dependency for backing the agent with
a real model; the demo works with no API key.

Backend A2A Console
===================

Open **Web → A2A Console**. The console discovers the site's Agent Card (live),
lets you pick a skill and **Delegate task**, and renders the streamed JSON-RPC
frames, the task state, the agent's messages, the cooperative ``input-required``
prompt and the returned artifact. Each task is recorded in
``tx_a2aintegration_task_log``. The **Skill Inspector** lists the advertised
skills and the lifecycle states.

Public A2A surface
==================

Two eIDs expose a genuine A2A surface:

:Agent Card:  ``/index.php?eID=a2a_card`` — the discoverable Agent Card (JSON).
:JSON-RPC:    ``/index.php?eID=a2a_rpc`` — ``message/stream`` (SSE) and
              ``message/send`` (single response). Rate-limited; the agent is
              deterministic and side-effect free, so it is safe to expose.

Frontend Concierge plugin
=========================

The Content Block ``a2a_concierge`` puts the site's agent in front of visitors.
The visitor picks a skill (or types a request) and watches the task move through
its lifecycle, including the inline ``input-required`` pause, before it streams an
artifact back. Completed requests are stored in ``tx_a2aintegration_request``.
The widget is theme-aware (light/dark) and exposes editor fields for the
headline/intro/placeholder, accent colour and an optional frame panel.

Demo placement in this lab
==========================

In the Webconsulting TYPO3 Lab the frontend plugin is live on the **desiderio**
site:

:Site:            typo3-vienna-camp-2026 (desiderio)
:Page:            The Desiderio ecosystem — page uid **1068**, slug ``/features``
:Content element: ``a2a_concierge``, colPos 0
:URL:             ``https://webconsulting-typo3-lab.ddev.site/desiderio/features``

The element is created by ``a2a:seed:demo`` (idempotent). The backend console is
at **Web → A2A Console**.

Server-Sent Events in TYPO3
===========================

All three surfaces stream via a small, dependency-free SSE encoder
(``SseEncoder::stream()`` — headers, ``echo`` + ``flush`` per frame, ``exit``).
Clients consume the stream with ``fetch()`` + ``ReadableStream`` because the call
is a POST (``EventSource`` is GET-only).
