CREATE INDEX idx_compte_vote_ip_compte
  ON public.compte_vote_ip (compte_vote_compte_cod ASC NULLS LAST);
CREATE INDEX idx_compte_vote_ip_pour_delain
  ON public.compte_vote_ip (compte_vote_pour_delain ASC NULLS LAST);
CREATE INDEX idx_compte_vote_ip_date
  ON public.compte_vote_ip (compte_vote_date ASC NULLS LAST);
CREATE INDEX idx_compte_vote_ip_verif
  ON public.compte_vote_ip (compte_vote_verifier ASC NULLS LAST);
