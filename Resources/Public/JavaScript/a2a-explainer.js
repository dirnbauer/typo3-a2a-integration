/**
 * A2A explainer — a looping GSAP animation that *teaches* the protocol: a client
 * agent (left) discovers the server agent's Agent Card, delegates a task that
 * flows right, the task works, pauses to ask for input (which flows back), then
 * the server streams an artifact back and the task completes. Uses the vendored
 * global `window.gsap`. Honors prefers-reduced-motion with a static end frame.
 */

const SHOTS = [
  { type: 'GET agent-card', dir: 'lr', c: '#0d9488', cap: 'The client discovers the server\'s Agent Card' },
  { type: 'message/send', dir: 'lr', c: '#2563eb', cap: 'It delegates a task over JSON-RPC' },
  { type: 'status: working', dir: 'rl', c: '#d97706', cap: 'The server accepts and starts working' },
  { type: 'status: input-required', dir: 'rl', c: '#7c3aed', cap: 'It pauses and asks for one more detail', pause: true },
  { type: 'message (answer)', dir: 'lr', c: '#2563eb', cap: 'The client answers — the task resumes' },
  { type: 'artifact-update', dir: 'rl', c: '#0d9488', cap: 'The result streams back as an artifact', art: true },
  { type: 'status: completed', dir: 'rl', c: '#16a34a', cap: 'Task completed — artifact delivered' },
];

function build(mount) {
  mount.innerHTML = `
    <div class="a2a-exp">
      <div class="a2a-exp__cap" data-cap>A2A: agents discover each other and delegate tasks</div>
      <div class="a2a-exp__stage">
        <div class="a2a-exp__node a2a-exp__node--a">
          <div class="a2a-exp__node-h">Client agent <small>caller</small></div>
          <div data-answer></div>
        </div>
        <div class="a2a-exp__lane"><span class="a2a-exp__chip" data-chip></span></div>
        <div class="a2a-exp__node a2a-exp__node--b">
          <div class="a2a-exp__node-h">Site agent <small>server</small></div>
          <div class="a2a-exp__badge" data-state>idle</div>
          <div data-art></div>
        </div>
      </div>
      <div class="a2a-exp__legend">
        <span style="color:#2563eb">submitted</span><span style="color:#d97706">working</span>
        <span style="color:#7c3aed">input-required</span><span style="color:#16a34a">completed</span>
      </div>
    </div>`;
  return {
    cap: mount.querySelector('[data-cap]'),
    chip: mount.querySelector('[data-chip]'),
    state: mount.querySelector('[data-state]'),
    art: mount.querySelector('[data-art]'),
    answer: mount.querySelector('[data-answer]'),
  };
}

function animate(mount) {
  const gsap = window.gsap;
  const el = build(mount);
  if (!gsap || window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    el.cap.textContent = 'A2A: agents discover each other via an Agent Card and delegate tasks that stream a lifecycle and artifacts back.';
    el.state.textContent = 'completed'; el.state.style.background = '#16a34a';
    return;
  }

  const t = gsap.timeline({ repeat: -1, repeatDelay: 1.1 });

  SHOTS.forEach((shot) => {
    t.add(() => {
      el.cap.textContent = shot.cap;
      el.chip.textContent = shot.type;
      el.chip.style.background = shot.c;
      if (shot.type.startsWith('status:')) { const s = shot.type.split(': ')[1]; el.state.textContent = s; el.state.style.background = shot.c; }
    });
    const from = shot.dir === 'lr' ? -95 : 95;
    const to = shot.dir === 'lr' ? 95 : -95;
    t.fromTo(el.chip, { x: from, autoAlpha: 0 }, { x: to, autoAlpha: 1, duration: 0.6, ease: 'power1.inOut' });
    t.to(el.chip, { autoAlpha: 0, duration: 0.15 });

    if (shot.pause) {
      t.add(() => { el.answer.innerHTML = '<span class="a2a-exp__art">▷ answering…</span>'; });
      t.to('.a2a-exp__lane', { opacity: 0.4, duration: 0.2 });
      t.to('.a2a-exp__lane', { opacity: 1, duration: 0.2, delay: 0.5 });
    }
    if (shot.art) {
      t.add(() => { el.art.innerHTML = '<span class="a2a-exp__art">📄 onboarding-plan.md</span>'; });
    }
    t.to({}, { duration: 0.35 });
  });

  t.to({}, { duration: 0.5 });
  t.add(() => { el.answer.innerHTML = ''; el.art.innerHTML = ''; el.state.textContent = 'idle'; el.state.style.background = '#94a3b8'; });
}

function ready(fn) { document.readyState !== 'loading' ? fn() : document.addEventListener('DOMContentLoaded', fn); }
ready(() => {
  const mount = document.querySelector('[data-a2a-explainer]');
  if (!mount) return;
  let tries = 0;
  (function waitGsap() {
    if (window.gsap || tries > 40) { animate(mount); return; }
    tries++; setTimeout(waitGsap, 50);
  })();
});

export {};
