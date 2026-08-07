<style>
    #cc-main {
        --cc-bg: #ffffff;
        --cc-primary-color: #373f50;
        --cc-secondary-color: #4b566b;
        --cc-link-color: #f27923;
        --cc-btn-primary-bg: #f27923;
        --cc-btn-primary-color: #ffffff;
        --cc-btn-primary-border-color: #f27923;
        --cc-btn-primary-hover-bg: #d85f0a;
        --cc-btn-primary-hover-color: #ffffff;
        --cc-btn-primary-hover-border-color: #d85f0a;
        --cc-btn-secondary-bg: #ffffff;
        --cc-btn-secondary-color: #373f50;
        --cc-btn-secondary-border-color: #eaded5;
        --cc-btn-secondary-hover-bg: #fff5ed;
        --cc-btn-secondary-hover-color: #f27923;
        --cc-btn-secondary-hover-border-color: rgba(242, 121, 35, 0.24);
        --cc-separator-border-color: #f0e3d9;
        --cc-toggle-on-bg: #f27923;
        --cc-toggle-off-bg: #b8c2ce;
        --cc-toggle-readonly-bg: #f8dfca;
        --cc-cookie-category-block-bg: #fff9f4;
        --cc-cookie-category-block-border: rgba(242, 121, 35, 0.12);
        --cc-cookie-category-block-hover-bg: #fff3e8;
        --cc-cookie-category-block-hover-border: rgba(242, 121, 35, 0.2);
        --cc-footer-bg: #fcf7f2;
        --cc-footer-color: #4b566b;
        --cc-footer-border-color: #f0e3d9;
        --cc-overlay-bg: rgba(43, 52, 69, 0.58);
        --cc-modal-border-radius: 1.25rem;
        --cc-btn-border-radius: 0.95rem;
        --cc-font-family: "Inter", sans-serif;
    }

    #cc-main .cm,
    #cc-main .pm {
        position: relative;
        border-radius: 1.25rem;
        border: 1px solid rgba(242, 121, 35, 0.1);
        background:
            radial-gradient(circle at top right, rgba(242, 121, 35, 0.08), transparent 32%),
            linear-gradient(180deg, #ffffff 0%, #fffcf9 100%);
        box-shadow: 0 24px 56px rgba(55, 63, 80, 0.18);
    }

    #cc-main .cm::before,
    #cc-main .pm::before {
        content: "";
        position: absolute;
        inset: 0 0 auto 0;
        height: 6px;
        background: linear-gradient(90deg, #f27923 0%, #ffb16f 100%);
    }

    #cc-main .cm {
        max-width: 42rem;
        padding: 0;
    }

    #cc-main .cm__title,
    #cc-main .pm__title {
        color: #373f50;
        font-weight: 700;
        letter-spacing: 0.01em;
    }

    #cc-main .cm__desc,
    #cc-main .pm__section-desc,
    #cc-main .pm__section-title {
        color: #4b5563;
        line-height: 1.55;
    }

    #cc-main a,
    #cc-main .cc__link {
        color: #f27923;
    }

    #cc-main .pm__header {
        padding-top: 1.35rem;
    }

    #cc-main .pm__body {
        padding-top: 0.5rem;
    }

    #cc-main .cm__title {
        margin-bottom: 0.9rem;
    }

    #cc-main .cm__desc {
        margin-bottom: 1.2rem;
    }

    #cc-main .cm__body {
        padding: 2.1rem 2.5rem 1.35rem;
    }

    #cc-main .cm__footer,
    #cc-main .pm__footer {
        border-top: 1px solid #f0e3d9;
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(8px);
    }

    #cc-main .cm__footer {
        padding: 1rem 2.5rem 2rem;
    }

    #cc-main .cm__btn,
    #cc-main .pm__btn {
        border-radius: 0.75rem;
        min-height: 2.7rem;
        font-weight: 700;
        transition: transform 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease, border-color 0.2s ease;
    }

    #cc-main .cm__btn:not(.cm__btn--secondary),
    #cc-main .pm__btn:not(.pm__btn--secondary) {
        box-shadow: 0 12px 26px rgba(242, 121, 35, 0.18);
    }

    #cc-main .cm__btn:not(.cm__btn--secondary):hover,
    #cc-main .pm__btn:not(.pm__btn--secondary):hover {
        transform: translateY(-1px);
        box-shadow: 0 16px 30px rgba(242, 121, 35, 0.22);
    }

    #cc-main .cm__btn-group {
        display: grid;
        grid-template-columns: 1fr;
        gap: 0;
    }

    #cc-main .cm__btn-group .cm__btn + .cm__btn {
        margin-top: 0.55rem;
    }

    #cc-main .cm__btn--secondary {
        border: 1px solid #eaded5;
        background: #fff;
        color: #373f50;
    }

    #cc-main .cm__btn--secondary:hover,
    #cc-main .pm__btn--secondary:hover {
        border-color: rgba(242, 121, 35, 0.24);
        background: #fff5ed;
        color: #f27923;
    }

    #cc-main .pm__badge {
        background: rgba(242, 121, 35, 0.1);
        color: #f27923;
        border-radius: 999px;
        padding: 0.28rem 0.7rem;
        font-weight: 700;
    }

    #cc-main .pm__section {
        border: 1px solid rgba(242, 121, 35, 0.12);
        border-radius: 1rem;
        background: #fff9f4;
        overflow: hidden;
        transition: border-color 0.2s ease, background-color 0.2s ease, box-shadow 0.2s ease;
    }

    #cc-main .pm__section:hover {
        border-color: rgba(242, 121, 35, 0.2);
        background: #fff3e8;
        box-shadow: 0 10px 24px rgba(242, 121, 35, 0.08);
    }

    #cc-main .pm__section-title {
        color: #373f50;
        font-weight: 700;
    }

    #cc-main .pm__service-icon {
        border-color: #f27923;
    }

    #cc-main .pm__section-arrow svg {
        stroke: #f27923;
    }

    .cookie-consent-trigger {
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
        width: 2.25rem;
        height: 2.25rem;
        padding: 0;
        border: 0;
        border-radius: 0.3125rem;
        background: #f27923;
        box-shadow: 0 0.5rem 1rem rgba(242, 121, 35, 0.18);
        transition: transform 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
        color: #ffffff !important;
    }

    .cookie-consent-trigger:hover {
        transform: translateY(-1px);
        background: #d85f0a;
        box-shadow: 0 0.75rem 1.5rem rgba(242, 121, 35, 0.28);
    }

    .cookie-consent-trigger:focus-visible {
        outline: 3px solid rgba(242, 121, 35, 0.28);
        outline-offset: 3px;
    }

    .cookie-consent-trigger img {
        display: block;
        width: 1rem;
        height: 1rem;
        flex-shrink: 0;
    }

    .cookie-consent-trigger--floating {
        position: fixed;
        bottom: 1.25rem;
        left: 1.25rem;
        z-index: 1045;
        width: 3rem;
        height: 3rem;
        border-radius: 50%;
        font-size: 1.15rem;
    }

    @media (max-width: 575.98px) {
        .cookie-consent-trigger--floating {
            bottom: .85rem;
            left: .85rem;
            width: 2.75rem;
            height: 2.75rem;
        }
    }

    @media (max-width: 575.98px) {
        #cc-main .cm__body,
        #cc-main .cm__footer {
            padding-left: 1.2rem;
            padding-right: 1.2rem;
        }
    }
</style>
