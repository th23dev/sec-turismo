const LAT = 0.39;
    const LON = 0.72;
    const COORD_LABEL = `${Math.abs(LAT).toFixed(2)}°${LAT >= 0 ? 'N' : 'S'}, ${Math.abs(LON).toFixed(2)}°${LON >= 0 ? 'E' : 'W'}`;
    let currentDate = new Date();
    currentDate.setHours(0, 0, 0, 0);

    function fmtDate(d) {
      return d.toISOString().split('T')[0];
    }

    /* ── Modelo harmônico local ─────────────────────────────────── */
    function calcTides(dateObj) {
      const knownNewMoon = new Date('2024-01-11T11:57:00Z');
      const synodicMonth = 29.530588853 * 24 * 3600 * 1000;
      const phase = ((dateObj - knownNewMoon) / synodicMonth % 1 + 1) % 1;

      const t0 = dateObj.getTime();
      const T = 12.4206 * 3600 * 1000;   /* período semidiurno */
      const moonAngle = phase * 2 * Math.PI;
      const baseAmp  = 2.2 + 0.8 * Math.cos(moonAngle);
      const meanLvl  = 1.5;
      const phOff    = phase * 2 * Math.PI * 0.5;

      /* Extremos */
      const extremes = [];
      for (let h = 0; h <= 24; h += 0.05) {
        const tMs = t0 + h * 3600 * 1000;
        const ang  = (tMs / T) * 2 * Math.PI + phOff;
        const ht   = meanLvl + baseAmp * Math.cos(ang);
        if (h > 0 && h < 24) {
          const prev = meanLvl + baseAmp * Math.cos(((tMs - 3*60*1000) / T) * 2 * Math.PI + phOff);
          const nxt  = meanLvl + baseAmp * Math.cos(((tMs + 3*60*1000) / T) * 2 * Math.PI + phOff);
          if (ht > prev && ht > nxt) extremes.push({ time: new Date(tMs), height: ht, type: 'alta' });
          else if (ht < prev && ht < nxt) extremes.push({ time: new Date(tMs), height: ht, type: 'baixa' });
        }
      }
      const filtered = [];
      for (const e of extremes) {
        if (!filtered.length || (e.time - filtered[filtered.length-1].time) > 3*3600*1000)
          filtered.push(e);
      }

      /* Série horária */
      const hourly = [];
      for (let h = 0; h <= 23; h++) {
        const tMs = t0 + h * 3600 * 1000;
        hourly.push(meanLvl + baseAmp * Math.cos((tMs / T) * 2 * Math.PI + phOff));
      }

      return { extremes: filtered.slice(0, 6), hourly, phase, baseAmp, meanLvl };
    }

    function getMoon(phase) {
      if (phase < 0.03 || phase > 0.97) return 'Lua Nova';
      if (phase < 0.22) return 'Crescente';
      if (phase < 0.28) return 'Quarto Crescente';
      if (phase < 0.47) return 'Gibosa Crescente';
      if (phase < 0.53) return 'Lua Cheia';
      if (phase < 0.72) return 'Gibosa Minguante';
      if (phase < 0.78) return 'Quarto Minguante';
      return 'Minguante';
    }

    /* ── Desenha gráfico SVG ────────────────────────────────────── */
    function drawChart(hourly, extremes, dateObj) {
      const area = document.getElementById('chart-area');
      const W = area.clientWidth || 660;
      const H = 180;
      const padB = 20;
      const max = Math.max(...hourly) + 0.2;
      const min = Math.min(...hourly) - 0.2;
      const range = max - min;

      const xs = hourly.map((_, i) => (i / 23) * W);
      const ys = hourly.map(v => (H - padB) - ((v - min) / range) * (H - padB));

      let path = `M${xs[0].toFixed(1)},${ys[0].toFixed(1)}`;
      for (let i = 1; i < xs.length; i++) {
        const cx = ((xs[i-1] + xs[i]) / 2).toFixed(1);
        path += ` C${cx},${ys[i-1].toFixed(1)} ${cx},${ys[i].toFixed(1)} ${xs[i].toFixed(1)},${ys[i].toFixed(1)}`;
      }
      const fill = path + ` L${xs[xs.length-1].toFixed(1)},${H-padB} L${xs[0].toFixed(1)},${H-padB} Z`;

      const isToday = fmtDate(dateObj) === fmtDate(new Date());
      let nowSvg = '';
      if (isToday) {
        const now = new Date();
        const nx = ((now.getHours() + now.getMinutes()/60) / 23 * W).toFixed(1);
        nowSvg = `
          <line x1="${nx}" y1="0" x2="${nx}" y2="${H-padB}" stroke="#D85A30" stroke-width="1.5" stroke-dasharray="4,3"/>
          <text x="${nx}" y="${H-padB+14}" text-anchor="middle" font-size="10" fill="#D85A30">agora</text>`;
      }

      area.innerHTML = `
        <svg width="100%" height="${H}" viewBox="0 0 ${W} ${H}" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
          <defs>
            <linearGradient id="wg" x1="0" y1="0" x2="0" y2="1">
              <stop offset="0%" stop-color="#378ADD" stop-opacity="0.3"/>
              <stop offset="100%" stop-color="#378ADD" stop-opacity="0.02"/>
            </linearGradient>
          </defs>
          <text x="10" y="16" font-size="11" fill="#555">${COORD_LABEL}</text>
          <path d="${fill}" fill="url(#wg)"/>
          <path d="${path}" fill="none" stroke="#378ADD" stroke-width="2.5" stroke-linejoin="round"/>
          ${[0,4,8,12,16,20,23].map(h => {
            const x = (h/23)*W;
            return `<text x="${x.toFixed(1)}" y="${H}" text-anchor="middle" font-size="10" fill="#9a9a96">${String(h).padStart(2,'0')}h</text>`;
          }).join('')}
          ${extremes.map(e => {
            const h = e.time.getHours() + e.time.getMinutes()/60;
            const x = (h/23)*W;
            const y = (H-padB) - ((e.height - min)/range)*(H-padB);
            const col = e.type === 'alta' ? '#185FA5' : '#3B6D11';
            return `
              <circle cx="${x.toFixed(1)}" cy="${y.toFixed(1)}" r="5" fill="${col}"/>
              <text x="${x.toFixed(1)}" y="${(y-10).toFixed(1)}" text-anchor="middle" font-size="11" font-weight="500" fill="${col}">${e.height.toFixed(2)}m</text>`;
          }).join('')}
          ${nowSvg}
        </svg>`;
    }

    /* ── Renderiza tudo ─────────────────────────────────────────── */
    function render(date) {
      const { extremes, hourly, phase, baseAmp, meanLvl } = calcTides(date);
      const altas  = extremes.filter(e => e.type === 'alta').map(e => e.height);
      const baixas = extremes.filter(e => e.type === 'baixa').map(e => e.height);
      const maxAlta  = altas.length  ? Math.max(...altas)  : 0;
      const minBaixa = baixas.length ? Math.min(...baixas) : 0;

      /* Textos de localização */
      const locationText = document.getElementById('location-text');
      if (locationText) locationText.textContent = `Coordenadas: ${COORD_LABEL} · Maré semidiurna · Fuso: Brasília (UTC-3)`;
      const sourceText = document.getElementById('source-text');
      if (sourceText) sourceText.textContent = `Modelo harmônico de marés semidiurnas · Open-Meteo Marine API (wave_height) · ${COORD_LABEL}`;

      /* Stats */
      document.getElementById('stats-grid').innerHTML = `
        <div class="stat-card">
          <div class="label">Maré máxima</div>
          <div class="value">${maxAlta.toFixed(2)} m</div>
          <div class="sub">preamar</div>
        </div>
        <div class="stat-card">
          <div class="label">Maré mínima</div>
          <div class="value">${minBaixa.toFixed(2)} m</div>
          <div class="sub">baixamar</div>
        </div>
        <div class="stat-card">
          <div class="label">Amplitude</div>
          <div class="value">${(maxAlta - minBaixa).toFixed(2)} m</div>
          <div class="sub">variação total</div>
        </div>
        <div class="stat-card">
          <div class="label">Fase lunar</div>
          <div class="value" style="font-size:15px;">${getMoon(phase)}</div>
          <div class="sub">${Math.round(phase * 100)}% do ciclo</div>
        </div>`;

      /* Tabela */
      document.getElementById('tide-tbody').innerHTML = extremes.map(e => {
        const hStr = e.time.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit', timeZone: 'America/Belem' });
        const coef = e.type === 'alta'
          ? Math.round(40 + (e.height / (meanLvl + baseAmp)) * 60)
          : Math.round(10 + (1 - e.height / (meanLvl + baseAmp)) * 30);
        return `<tr>
          <td><span class="badge badge-${e.type}">${e.type === 'alta' ? 'Preamar' : 'Baixamar'}</span></td>
          <td>${hStr}</td>
          <td><strong>${e.height.toFixed(2)}</strong></td>
          <td>${coef}</td>
        </tr>`;
      }).join('');

      /* Gráfico (após layout) */
      requestAnimationFrame(() => drawChart(hourly, extremes, date));
    }

    function setDate(d) {
      currentDate = new Date(d);
      currentDate.setHours(0, 0, 0, 0);
      document.getElementById('date-input').value = fmtDate(currentDate);
      render(currentDate);
    }

    /* ── Evento de navegação ────────────────────────────────────── */
    document.getElementById('date-input').value = fmtDate(currentDate);
    document.getElementById('btn-prev').onclick  = () => { const d = new Date(currentDate); d.setDate(d.getDate()-1); setDate(d); };
    document.getElementById('btn-next').onclick  = () => { const d = new Date(currentDate); d.setDate(d.getDate()+1); setDate(d); };
    document.getElementById('btn-hoje').onclick  = () => setDate(new Date());
    document.getElementById('date-input').onchange = e => {
      const [y,m,d] = e.target.value.split('-');
      setDate(new Date(y, m-1, d));
    };

    render(currentDate);
    window.addEventListener('resize', () => {
      const { extremes, hourly } = calcTides(currentDate);
      drawChart(hourly, extremes, currentDate);
    });