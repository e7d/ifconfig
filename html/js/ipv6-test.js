(() => {
    const $modal = document.querySelector('.modal');
    const $modalTitle = $modal.querySelector('.modal-title');
    const $modalContent = $modal.querySelector('.modal-content');
    const $progressBar = document.querySelector('.progress-bar');
    const $scoreValue = document.querySelector('.score-value');
    const $scoreDetails = document.querySelectorAll('.score-details');
    const $scoreValues = {
        'ipv4': document.querySelector('.score-ipv4 .value'),
        'ipv6': document.querySelector('.score-ipv6 .value'),
        'fallback': document.querySelector('.score-fallback .value'),
        'ipv6-type': document.querySelector('.score-ipv6-type .value'),
        'icmpv6': document.querySelector('.score-icmpv6 .value')
    };
    const $browserInfo = document.querySelector('#browser-info');
    const $ipv4Info = document.querySelector('#ipv4-info');
    const $ipv6Info = document.querySelector('#ipv6-info');
    const $node = {
        browser: {
            default: $browserInfo.querySelector('.default'),
            fallback: $browserInfo.querySelector('.fallback'),
        },
        4: {
            ip: $ipv4Info.querySelector('.ip'),
            address: $ipv4Info.querySelector('.address'),
            hostname: $ipv4Info.querySelector('.hostname'),
            asn: $ipv4Info.querySelector('.asn'),
        },
        6: {
            ip: $ipv6Info.querySelector('.ip'),
            address: $ipv6Info.querySelector('.address'),
            hostname: $ipv6Info.querySelector('.hostname'),
            asn: $ipv6Info.querySelector('.asn'),
            type: $ipv6Info.querySelector('.type'),
            slaac: $ipv6Info.querySelector('.slaac'),
            mac: $ipv6Info.querySelectorAll('.mac'),
            macAddress: $ipv6Info.querySelector('.mac-address'),
            macVendor: $ipv6Info.querySelector('.mac-vendor'),
            icmp: $ipv6Info.querySelector('.icmp'),
        },
    }

    $scoreValue.addEventListener('click', () => {
        for (const $scoreDetail of $scoreDetails) {
            $scoreDetail.classList.toggle('visible');
        }
    });

    for (const [, $label] of document.querySelectorAll('label[for="modal"]').entries()) {
        $label.addEventListener('click', () => {
            $modalTitle.textContent = $label.textContent;
            $modalContent.textContent = $label.title;
        });
    }

    const scoreKeys = [];
    const scoreValues = {
        'ipv4': 2,
        'ipv6': 10,
        'fast_fallback': 3,
        'slow_fallback': 1,
        'ipv6_native': 2,
        'ipv6_not_slaac': 1,
        'icmpv6': 2,
    };
    const scoreDetails = {
        'ipv4': ['ipv4'],
        'ipv6': ['ipv6'],
        'fallback': ['fast_fallback', 'slow_fallback'],
        'ipv6-type': ['ipv6_native', 'ipv6_not_slaac'],
        'icmpv6': ['icmpv6'],
    };

    function updateScore(key) {
        if (scoreKeys.includes(key)) return;
        scoreKeys.push(key);
        let score = scoreKeys.reduce((acc, key) => acc + scoreValues[key], 0);
        if (score < 0) score = 0;
        if (score > 20) score = 20;
        $progressBar.style.width = `${score * 5}%`;
        $progressBar.className = `progress-bar ${score < 10 ? 'danger' : score < 20 ? 'warning' : 'success'}`;
        $scoreValue.textContent = `${score} / 20`;
        for (const [detail, keys] of Object.entries(scoreDetails)) {
            $scoreValues[detail].textContent = keys.reduce((acc, key) => acc + scoreKeys.includes(key) * scoreValues[key], 0);
        }
    }

    async function getInfo(ipVersion) {
        try {
            const response = await fetch(ENDPOINTS[ipVersion]);
            return response.json();
        } catch (error) {
            console.error(error);
            return null;
        }
    }

    async function ping6() {
        try {
            const response = await fetch(ENDPOINTS.ping6);
            const ping = (await response.text()).trim();
            return ping !== '-1';
        } catch (error) {
            return false;
        }
    }

    function setBrowserResults(type, defaultInfo, fallbackInfo) {
        if (type === 'default') {
            $node.browser.default.innerHTML = defaultInfo ? toLabel(`IPv${defaultInfo.ip.version}`, 'success') : toLabel('None', 'danger');
            return;
        }
        if (!defaultInfo?.ip || !fallbackInfo?.ip || defaultInfo.ip.version === fallbackInfo.ip.version) {
            $node.browser.fallback.innerHTML = toLabel('None', 'danger');
            return;
        }
        const resources = performance.getEntriesByType("resource");
        const fallbackPerformance = fallbackInfo
            ? resources.find(({
                name
            }) => name.endsWith(ENDPOINTS[fallbackInfo.ip.version]))
            : null;
        const fallbackTime = fallbackPerformance ? Math.round(fallbackPerformance.duration) : null;
        const fallbackColor = fallbackTime < 1000 ? 'success' : 'warning';
        $node.browser.fallback.innerHTML = fallbackInfo
            ? `${toLabel(`IPv${fallbackInfo.ip.version}`, fallbackColor)} ${fallbackTime ? `in ${fallbackTime} ms` : ''}`
            : toLabel('None', 'danger');
        updateScore(fallbackTime < 1000 ? 'fast_fallback' : 'slow_fallback');
    }

    function toAsnString(country, asn) {
        return asn ? `${country.flag.emoji ?? ''} AS${asn.number} ${asn.org}` : toLabel('N/A', 'warning');
    }

    function toLabel(text, color, big) {
        return `<span class="label label-${color} ${big ? 'label-big' : ''}">${text}</span>`;
    }

    function toPingStatus(ping) {
        return {
            undefined: '<span class="loader"></span>',
            false: toLabel('Filtered', 'warning')
        }[ping] || `${toLabel('Success', 'success')} in ${ping} ms`;
    }

    function setConnectivityResultsValues(ipVersion, results, ping) {
        $node[ipVersion].ip.innerHTML = results ? toLabel('✓', 'success') : toLabel('✗', 'danger');
        if (!results) {
            $node[ipVersion].address.textContent = '-';
            $node[ipVersion].hostname.textContent = '-';
            $node[ipVersion].asn.textContent = '-';
            if (ipVersion === 6) {
                $node[ipVersion].type.textContent = '-';
                $node[ipVersion].slaac.textContent = '-';
                for (const $mac of $node[ipVersion].mac) {
                    $mac.style.display = 'none';
                }
                $node[ipVersion].icmp.textContent = '-';
            }
        }
        const { ip, host, asn, country } = results;
        $node[ip.version].address.textContent = ip.address;
        $node[ip.version].hostname.textContent = host ?? ip.address;
        $node[ip.version].asn.innerHTML = toAsnString(country, asn);
        if (ip.version === 6) {
            if (ip.type === 'native') updateScore('ipv6_native');
            if (!ip.slaac) updateScore('ipv6_not_slaac');
            $node[ip.version].type.innerHTML = ip.type === 'native' ? toLabel('Native', 'success') : toLabel(ip.type, 'warning');
            $node[ip.version].slaac.innerHTML = ip.slaac ? toLabel('Yes', 'warning') : toLabel('No', 'success');
            if (ip.slaac) {
                for (const $mac of $node[ip.version].mac) {
                    $mac.style.display = 'flex';
                }
                $node[ip.version].macAddress.textContent = ip.mac.address;
                $node[ip.version].macVendor.textContent = ip.mac.vendor;
            }
            $node[ip.version].icmp.innerHTML = toPingStatus(ping);
            if (ping) updateScore('icmpv6');
        }
    }

    async function setConnectivityResults(results, ipVersion) {
        if (!results?.ip) {
            setConnectivityResultsValues(ipVersion, null);
            return;
        }
        updateScore(`ipv${results.ip.version}`);
        const { ip } = results;
        setConnectivityResultsValues(ip.version, results);
        let ping;
        if (ip.version === 6) {
            if (ip.type === 'native') updateScore('ipv6_native');
            if (!ip.slaac) updateScore('ipv6_not_slaac');
            setConnectivityResultsValues(ip.version, results);
            ping = await ping6();
            if (ping) updateScore('icmpv6');
            setConnectivityResultsValues(ip.version, results, ping);
        }
    }

    async function ipv6Test() {
        const defaultInfo = await getInfo('auto');
        setConnectivityResults(defaultInfo);
        setBrowserResults('default', defaultInfo);
        const fallbackIpVersion = defaultInfo.ip.version === 4 ? 6 : 4;
        const fallbackInfo = await getInfo(fallbackIpVersion);
        setConnectivityResults(fallbackInfo, fallbackIpVersion);
        setBrowserResults('fallback', defaultInfo, fallbackInfo);
    }

    ipv6Test();
})();
