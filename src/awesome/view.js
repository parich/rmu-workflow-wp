/**
 * view.js — RMU Workflow frontend script
 * Fetches flowchart data from the API and renders an interactive list
 * with search and tag filtering.
 */
( function () {
	'use strict';

	const settings = window.rmuWorkflowSettings || {};
	const API_URL  = ( settings.apiUrl  || 'https://workflow.rmu.ac.th/api/embed/flowcharts' ).replace( /\/$/, '' );
	const BASE_URL = ( settings.baseUrl || 'https://workflow.rmu.ac.th' ).replace( /\/$/, '' );

	// ------------------------------------------------------------------
	// Helpers
	// ------------------------------------------------------------------

	function formatThaiDate( iso ) {
		if ( ! iso ) return '';
		try {
			return new Date( iso ).toLocaleDateString( 'th-TH', {
				year:  'numeric',
				month: 'short',
				day:   'numeric',
			} );
		} catch {
			return '';
		}
	}

	function escHtml( str ) {
		const div = document.createElement( 'div' );
		div.appendChild( document.createTextNode( str || '' ) );
		return div.innerHTML;
	}

	// ------------------------------------------------------------------
	// Render
	// ------------------------------------------------------------------

	function buildThumbnail( item ) {
		if ( item.previewUrl && item.mediaType !== 'PDF' ) {
			return `<img
				class="rmu-workflow-thumb-img"
				src="${ escHtml( item.previewUrl ) }"
				alt="${ escHtml( item.title ) }"
				loading="lazy"
			/>`;
		}

		const isPdf     = item.mediaType === 'PDF';
		const iconPath  = isPdf
			? 'M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zm-1 1.5L18.5 9H13V3.5zM8.5 17.5v-5h1.3c.7 0 1.2.2 1.5.5.3.3.5.8.5 1.4 0 .6-.2 1-.5 1.3-.3.3-.8.5-1.5.5H9.8v1.3H8.5zm1.3-2.2h.3c.3 0 .5-.1.6-.2.1-.1.2-.3.2-.6 0-.3-.1-.5-.2-.6-.1-.1-.3-.2-.6-.2h-.3v1.6zm3.4 2.2v-5h1.4c.6 0 1.1.2 1.4.5.6.6.6 1.8 0 2.4v.1c-.4.3-.8.5-1.4.5h-1.4v1.5zm1.3-2.4h.2c.2 0 .4-.1.5-.2.2-.2.2-.6 0-.8-.1-.1-.3-.2-.5-.2h-.2v1.2z'
			: 'M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z';

		return `<span class="rmu-workflow-thumb-icon ${ isPdf ? 'is-pdf' : 'is-image' }" aria-hidden="true">
			<svg viewBox="0 0 24 24" fill="currentColor" width="24" height="24">
				<path d="${ iconPath }"/>
			</svg>
		</span>`;
	}

	function renderItem( item ) {
		const href     = item.url
			? ( item.url.startsWith( 'http' ) ? item.url : BASE_URL + item.url )
			: '#';
		const date     = formatThaiDate( item.publishedAt );
		const dept     = item.departmentName
			? `<span class="rmu-workflow-dept" title="${ escHtml( item.departmentName ) }">${ escHtml( item.departmentName ) }</span>`
			: '';
		const tagsHtml = ( item.tags || [] ).slice( 0, 3 )
			.map( ( t ) => `<span class="rmu-workflow-tag-badge">#${ escHtml( t ) }</span>` )
			.join( '' );
		const metaHtml = ( dept || date )
			? `<div class="rmu-workflow-footer">
				${ dept }
				${ date ? `<span class="rmu-workflow-date">${ escHtml( date ) }</span>` : '' }
			</div>`
			: '';

		return `<li class="rmu-workflow-item">
			<a class="rmu-workflow-item-link" href="${ escHtml( href ) }" target="_blank" rel="noopener noreferrer">
				<div class="rmu-workflow-thumb">${ buildThumbnail( item ) }</div>
				<div class="rmu-workflow-meta">
					${ metaHtml }
					<p class="rmu-workflow-title">${ escHtml( item.title ) }</p>
					${ tagsHtml ? `<div class="rmu-workflow-tags-row">${ tagsHtml }</div>` : '' }
				</div>
				<span class="rmu-workflow-external" aria-hidden="true">
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
						<path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
						<polyline points="15 3 21 3 21 9"/>
						<line x1="10" y1="14" x2="21" y2="3"/>
					</svg>
				</span>
			</a>
		</li>`;
	}

	// ------------------------------------------------------------------
	// Widget class
	// ------------------------------------------------------------------

	class WorkflowWidget {
		constructor( el ) {
			this.el         = el;
			this.deptId     = el.dataset.deptId;
			this.allItems   = [];
			this.allTags    = [];
			this.activeTag  = null;
			this.searchTerm = '';

			this.listEl   = el.querySelector( '.rmu-workflow-list' );
			this.tagsEl   = el.querySelector( '.rmu-workflow-tags' );
			this.statusEl = el.querySelector( '.rmu-workflow-status' );
			this.searchEl = el.querySelector( '.rmu-workflow-search' );

			this._bindEvents();
			this._fetchData();
		}

		_bindEvents() {
			let timer;
			this.searchEl.addEventListener( 'input', ( e ) => {
				clearTimeout( timer );
				timer = setTimeout( () => {
					this.searchTerm = e.target.value.trim().toLowerCase();
					this._render();
				}, 250 );
			} );
		}

		async _fetchData() {
			this._setStatus( 'กำลังโหลด...' );
			try {
				const res  = await fetch( `${ API_URL }?deptId=${ encodeURIComponent( this.deptId ) }` );
				if ( ! res.ok ) { this._setStatus( '' ); return; }

				const data       = await res.json();
				const flowcharts = Array.isArray( data.flowcharts ) ? data.flowcharts : [];
				if ( ! flowcharts.length ) { this._setStatus( '' ); return; }

				this.allItems = flowcharts;
				this.allTags  = this._collectTags( flowcharts );
				this._setStatus( '' );
				this._renderTagPills();
				this._render();
			} catch {
				this._setStatus( '' );
			}
		}

		_collectTags( items ) {
			const freq = {};
			items.forEach( ( item ) => {
				( item.tags || [] ).forEach( ( t ) => { freq[ t ] = ( freq[ t ] || 0 ) + 1; } );
			} );
			return Object.keys( freq ).sort( ( a, b ) => freq[ b ] - freq[ a ] );
		}

		_renderTagPills() {
			if ( ! this.allTags.length ) return;

			this.tagsEl.innerHTML =
				`<button type="button" class="rmu-workflow-tag-pill is-active" data-tag="">ทั้งหมด</button>` +
				this.allTags.map( ( t ) =>
					`<button type="button" class="rmu-workflow-tag-pill" data-tag="${ escHtml( t ) }">#${ escHtml( t ) }</button>`
				).join( '' );

			this.tagsEl.querySelectorAll( '.rmu-workflow-tag-pill' ).forEach( ( btn ) => {
				btn.addEventListener( 'click', () => {
					this.activeTag = btn.dataset.tag || null;
					this.tagsEl.querySelectorAll( '.rmu-workflow-tag-pill' ).forEach( ( b ) => {
						b.classList.toggle( 'is-active', b === btn );
					} );
					this._render();
				} );
			} );
		}

		_getFiltered() {
			return this.allItems.filter( ( item ) => {
				if ( this.activeTag && ! ( item.tags || [] ).includes( this.activeTag ) ) return false;
				if ( this.searchTerm ) {
					const haystack = [ item.title, ...( item.tags || [] ) ].join( ' ' ).toLowerCase();
					if ( ! haystack.includes( this.searchTerm ) ) return false;
				}
				return true;
			} );
		}

		_render() {
			const filtered = this._getFiltered();
			this.listEl.innerHTML = filtered.length
				? filtered.map( renderItem ).join( '' )
				: `<li class="rmu-workflow-empty">ไม่พบ flowchart ที่ตรงกับเงื่อนไข</li>`;
		}

		_setStatus( msg ) {
			this.statusEl.textContent   = msg;
			this.statusEl.style.display = msg ? '' : 'none';
		}
	}

	// ------------------------------------------------------------------
	// Init
	// ------------------------------------------------------------------
	function init() {
		document.querySelectorAll( '.rmu-workflow-container' ).forEach( ( el ) => new WorkflowWidget( el ) );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
} )();
