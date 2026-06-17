# A2A Integration for TYPO3

**Make your TYPO3 site a first-class participant in the agent economy: it publishes an Agent Card, and other agents discover it and delegate tasks over a streamed, standard protocol.**

[A2A](https://a2a-protocol.org/) (the *Agent2Agent Protocol*, originally from Google, now a Linux Foundation project) is an open standard for **how independent AI agents discover and collaborate with each other**. One agent publishes an **Agent Card** — a small JSON document saying who it is, where to reach it, and what *skills* it offers. Another agent fetches that card and sends it a **task** over JSON-RPC; the serving agent streams back a **task lifecycle** (`submitted → working → input-required → completed`) and returns its work as **artifacts**. Crucially, an agent can **pause mid-task and ask the caller for one more detail** (`input-required`) — collaboration, not fire-and-forget.

This extension makes your TYPO3 site one of those agents, with two things you can click today:

1. **A2A Console** — a backend module that plays a *client agent*: it discovers the site's own Agent Card, delegates a task, and shows the lifecycle streaming in (including the cooperative `input-required` pause) with the artifact it gets back.
2. **A2A Concierge** — a frontend Content Block: the site's agent, made visitor-facing. A visitor delegates a task (summarise, draft, plan) and watches it complete, then receives an artifact.

It is part of a family of "agent stack" demo extensions for TYPO3: **[MCP] ↔ tools, A2A ↔ other agents, [AG‑UI] ↔ the user, [A2UI] ↔ the UI surface, [UCP] ↔ merchants, [AP2] ↔ payment authorization.** A2A is the **agent ↔ agent** layer.

---

## Why this is good

**For editors**
- **A team-mate, not a black box.** Delegate "summarise this page", "draft an outreach email", "plan onboarding" and get a usable artifact back — while watching exactly what the agent does at each step.
- **It asks before it assumes.** When the task needs a decision (e.g. *who is the audience?*), the agent pauses and asks you, instead of guessing. You stay in control of the outcome.

**For companies**
- **Interoperability without integrations.** A2A is an open standard backed by the Linux Foundation. Any A2A-capable agent — yours, a partner's, a customer's — can discover your site's agent from its Agent Card and work with it. No bespoke API per partner.
- **Discoverable by design.** The Agent Card is the contract: it advertises skills, transport and capabilities. Publish once; every compliant client knows how to call you.
- **Auditable delegation.** Every task is logged (skill, final state, frames, artifacts) — a clean record of what was delegated and how it ended.
- **Composes with the stack.** A2A coordinates *agents*; pair it with MCP (tools), AG‑UI (the human), A2UI (the UI) and UCP/AP2 (commerce) for a complete agentic surface.

---

## What you get

- **Backend module “A2A Console”** with two views:
  - **Console** — discovers the site's Agent Card (live), lets you pick a skill and delegate a task, and renders the streamed JSON-RPC frames, the task state, the agent's messages, the cooperative `input-required` prompt, and the returned artifact. Plus a per-day task log.
  - **Skill Inspector** — the skills the agent advertises and the lifecycle states a task moves through.
- **A GSAP animation** that teaches the protocol: a client agent discovers the card, delegates a task that flows to the server, the task works, pauses for input, then streams an artifact back and completes.
- **A real, public A2A surface** — an Agent Card endpoint and a JSON-RPC server (`message/stream` + `message/send`) another agent can actually call.
- **Frontend Content Block “A2A Concierge”** — the site's agent, visitor-facing, with skill chips, the inline `input-required` pause, a streamed artifact, theme-aware styling and an optional "under the hood" frame panel.
- **Server-Sent Events in pure PHP** — a small, dependency-free SSE encoder.
- **A deterministic agent** that always works offline (no API key) — and a clean seam to put a real LLM behind the same frames.
- **An idempotent seed command** so the demo content is reproducible.

---

## Live demo in this lab

| Surface | Where |
| --- | --- |
| Backend module | **Web → A2A Console** (Console + Skill Inspector) |
| Public Agent Card | `…/index.php?eID=a2a_card` |
| Public JSON-RPC | `…/index.php?eID=a2a_rpc` (`message/stream`, `message/send`) |
| Frontend plugin | Desiderio site → **The Desiderio ecosystem** page (`/desiderio/features`), seeded by `a2a:seed:demo` |

Try the cooperative pause: pick **Draft an outreach email** and delegate — the task reaches `input-required` and asks who the audience is; answer it and the task resumes, completes and returns `outreach-email.md`.

---

## Requirements

- TYPO3 **v14.3+**, PHP **8.3+**
- [Content Blocks](https://extensions.typo3.org/extension/content_blocks) (for the frontend plugin) — a soft dependency
- Optional: [`netresearch/nr-llm`](https://github.com/netresearch/t3x-nr-llm) to back the agent with a real model later (the demo needs no API key)

---

## Installation

```bash
composer require webconsulting/a2a-integration
ddev typo3 extension:setup
ddev typo3 cache:flush
# optional: place the frontend demo on a page (idempotent)
ddev typo3 a2a:seed:demo --page=1068
```

Open **Web → A2A Console** in the backend.

---

## A2A in one task

**1. Discover** — a client fetches the Agent Card:

```bash
curl "https://example.org/index.php?eID=a2a_card"
```

```json
{
  "protocolVersion": "0.3.0",
  "name": "TYPO3 Site Agent",
  "url": "https://example.org/index.php?eID=a2a_rpc",
  "capabilities": { "streaming": true },
  "skills": [
    { "id": "summarize_page", "name": "Summarise a page", "tags": ["content"] },
    { "id": "draft_outreach", "name": "Draft an outreach email", "tags": ["email"] }
  ]
}
```

**2. Delegate** — the client POSTs a JSON-RPC `message/stream` to the card's `url`:

```json
{ "jsonrpc": "2.0", "id": 1, "method": "message/stream",
  "params": { "message": { "role": "user",
    "parts": [{ "kind": "text", "text": "Draft an outreach email" }],
    "metadata": { "skill": "draft_outreach" } } } }
```

**3. Stream** — the server streams the lifecycle back as SSE (one JSON-RPC response per `data:` frame):

```
data: {"result":{"kind":"task","id":"task-…","status":{"state":"submitted"}}}
data: {"result":{"kind":"status-update","status":{"state":"working","message":{…}}}}
data: {"result":{"kind":"status-update","status":{"state":"input-required","message":{"parts":[{"text":"Who is the audience?"}]}},"final":true}}
```

**4. Cooperate** — the client answers by sending another message with the same `taskId` and `metadata.resume = true`; the task resumes:

```
data: {"result":{"kind":"status-update","status":{"state":"working"}}}
data: {"result":{"kind":"artifact-update","artifact":{"name":"outreach-email.md","parts":[{"text":"Subject: …"}]},"append":true}}
data: {"result":{"kind":"status-update","status":{"state":"completed"},"final":true}}
```

---

## The task lifecycle

| State | Meaning |
| --- | --- |
| `submitted` | Task created and acknowledged; not started yet. |
| `working` | The agent is actively processing. |
| `input-required` | Paused — the agent needs one more detail from the caller. |
| `completed` | Finished successfully; artifacts are available. |
| `failed` / `canceled` | Terminated by an error / by the caller. |

Browse the states and the advertised skills in **A2A Console → Skill Inspector**.

---

## Real LLM (optional)

The shipped `TaskRunner` is deterministic so the demo always works. To drive a skill with a real model, call the model inside `TaskRunner::run()` and emit the same frames — `nr-llm` (with its vault-backed provider) is a natural fit and is listed as a `suggest`. **The wire shape — Agent Card + JSON-RPC lifecycle — is identical either way, so no client changes.**

---

## Architecture

```
Public A2A surface                         Backend                         Frontend
  AgentCardEndpoint  (eID a2a_card)          A2aController (Console+Inspector)  concierge Content Block
  RpcEndpoint        (eID a2a_rpc) ─┐        SendController ─ AJAX SSE ─┐       a2a-concierge.js ─ eID SSE ─┐
  ConciergeEndpoint  (eID a2a_concierge) ────────────────────────────────────────────────────────────────┤
                                    ▼                                   ▼                                  ▼
                              TaskRunner (deterministic agent, per-skill scripts)
                                    │  emits JSON-RPC frames via Protocol\Frames
                              SseEncoder.stream()  → echo+flush+exit (SSE)
                                    │
                              TaskLogger → tx_a2aintegration_task_log      RequestStore → tx_a2aintegration_request
```

- `Classes/Service/AgentCard.php` — builds the discoverable Agent Card.
- `Classes/Service/SkillCatalog.php` — the advertised skills (single source of truth).
- `Classes/Protocol/Frames.php` — JSON-RPC frame factories (`task` / `status-update` / `artifact-update`).
- `Classes/Service/TaskRunner.php` — the agent: walks a task through its lifecycle, including the `input-required` pause.
- `Classes/Service/SseEncoder.php` — pure-PHP SSE.

---

## Commands

```bash
ddev typo3 a2a:seed:demo [--page=1068] [--sorting=2900] [--header="…"]
```

Idempotent: re-running refreshes the existing element on the page instead of duplicating it.

---

## License

GPL‑2.0‑or‑later. Part of the Webconsulting TYPO3 agent‑protocol family.

## Resources

- A2A Protocol — https://a2a-protocol.org/
- A2A specification & SDKs — https://github.com/a2aproject/A2A
