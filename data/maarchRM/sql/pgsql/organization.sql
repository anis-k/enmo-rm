DELETE FROM "organization"."archivalProfileAccess";
DELETE FROM "organization"."userPosition";
DELETE FROM "organization"."servicePosition";
DELETE FROM "organization"."orgContact";
DELETE FROM "organization"."organization";
DELETE FROM "organization"."orgType";

INSERT INTO organization."orgType" (code, name) VALUES ('Collectivite', 'Collectivité');
INSERT INTO organization."orgType" (code, name) VALUES ('Societe', 'Société');
INSERT INTO organization."orgType" (code, name) VALUES ('Direction', 'Direction d''une entreprise ou d''une collectivité');
INSERT INTO organization."orgType" (code, name) VALUES ('Service', 'Service d''une entreprise ou d''une collectivité');
INSERT INTO organization."orgType" (code, name) VALUES ('Division', 'Division d''une entreprise');

INSERT INTO organization.organization ("orgId", "orgName", "otherOrgName", "displayName", "registrationNumber", "beginDate", "endDate", "legalClassification", "businessType", description, "orgTypeCode", "orgRoleCodes", "taxIdentifier", "parentOrgId", "ownerOrgId", "isOrgUnit") VALUES ('ACME', 'Archives Conservation et Mémoire Électronique', NULL, 'Archives Conservation et Mémoire Électronique', 'ACME', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, false);
INSERT INTO organization.organization ("orgId", "orgName", "otherOrgName", "displayName", "registrationNumber", "beginDate", "endDate", "legalClassification", "businessType", description, "orgTypeCode", "orgRoleCodes", "taxIdentifier", "parentOrgId", "ownerOrgId", "isOrgUnit") VALUES ('DAF', 'Direction Administrative et Financière', NULL, 'Direction Administrative et Financière', 'DAF', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'ACME', 'ACME', true);
INSERT INTO organization.organization ("orgId", "orgName", "otherOrgName", "displayName", "registrationNumber", "beginDate", "endDate", "legalClassification", "businessType", description, "orgTypeCode", "orgRoleCodes", "taxIdentifier", "parentOrgId", "ownerOrgId", "isOrgUnit") VALUES ('SJ', 'Service Juridique', NULL, 'Service Juridique', 'SJ', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'DAF', 'ACME', true);
INSERT INTO organization.organization ("orgId", "orgName", "otherOrgName", "displayName", "registrationNumber", "beginDate", "endDate", "legalClassification", "businessType", description, "orgTypeCode", "orgRoleCodes", "taxIdentifier", "parentOrgId", "ownerOrgId", "isOrgUnit") VALUES ('RH', 'Direction des Ressources Humaines', NULL, 'Direction des Ressources Humaines', 'RH', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'ACME', 'ACME', true);
INSERT INTO organization.organization ("orgId", "orgName", "otherOrgName", "displayName", "registrationNumber", "beginDate", "endDate", "legalClassification", "businessType", description, "orgTypeCode", "orgRoleCodes", "taxIdentifier", "parentOrgId", "ownerOrgId", "isOrgUnit") VALUES ('GIC', 'Gestion et Conservation de l''Information', NULL, 'Gestion et Conservation de l''Information', 'GIC', NULL, NULL, NULL, NULL, NULL, NULL, 'owner', NULL, 'ACME', 'ACME', true);
INSERT INTO organization.organization ("orgId", "orgName", "otherOrgName", "displayName", "registrationNumber", "beginDate", "endDate", "legalClassification", "businessType", description, "orgTypeCode", "orgRoleCodes", "taxIdentifier", "parentOrgId", "ownerOrgId", "isOrgUnit") VALUES ('DG', 'Direction Générale', NULL, 'Direction Générale', 'DG', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'DAF', 'ACME', true);
INSERT INTO organization.organization ("orgId", "orgName", "otherOrgName", "displayName", "registrationNumber", "beginDate", "endDate", "legalClassification", "businessType", description, "orgTypeCode", "orgRoleCodes", "taxIdentifier", "parentOrgId", "ownerOrgId", "isOrgUnit") VALUES ('SG', 'Services généraux', NULL, 'Direction des Services Généraux', 'SG', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'DAF', 'ACME', true);
INSERT INTO organization.organization ("orgId", "orgName", "otherOrgName", "displayName", "registrationNumber", "beginDate", "endDate", "legalClassification", "businessType", description, "orgTypeCode", "orgRoleCodes", "taxIdentifier", "parentOrgId", "ownerOrgId", "isOrgUnit") VALUES ('DOCSOC', 'Documents de société', NULL, 'Document de société', 'DOCSOC', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'DG', 'ACME', true);
INSERT INTO organization.organization ("orgId", "orgName", "otherOrgName", "displayName", "registrationNumber", "beginDate", "endDate", "legalClassification", "businessType", description, "orgTypeCode", "orgRoleCodes", "taxIdentifier", "parentOrgId", "ownerOrgId", "isOrgUnit") VALUES ('LITIGES', 'Suivi litiges Contentieux', NULL, 'Suivi des litiges et contentieux', 'LITIGES', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'DG', 'ACME', true);
INSERT INTO organization.organization ("orgId", "orgName", "otherOrgName", "displayName", "registrationNumber", "beginDate", "endDate", "legalClassification", "businessType", description, "orgTypeCode", "orgRoleCodes", "taxIdentifier", "parentOrgId", "ownerOrgId", "isOrgUnit") VALUES ('ASSU', 'Assurances Groupe', NULL, 'Assurances du Groupe', 'ASSU', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'SJ', 'ACME', true);
INSERT INTO organization.organization ("orgId", "orgName", "otherOrgName", "displayName", "registrationNumber", "beginDate", "endDate", "legalClassification", "businessType", description, "orgTypeCode", "orgRoleCodes", "taxIdentifier", "parentOrgId", "ownerOrgId", "isOrgUnit") VALUES ('DSI', 'Direction des SI', NULL, 'Direction des Systèmes d''Information', 'DSI', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'ACME', 'ACME', true);
INSERT INTO organization.organization ("orgId", "orgName", "otherOrgName", "displayName", "registrationNumber", "beginDate", "endDate", "legalClassification", "businessType", description, "orgTypeCode", "orgRoleCodes", "taxIdentifier", "parentOrgId", "ownerOrgId", "isOrgUnit") VALUES ('DSOC', 'Droit des sociétés', NULL, 'Droit des Sociétés', 'DSOC', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'SJ', 'ACME', true);
INSERT INTO organization.organization ("orgId", "orgName", "otherOrgName", "displayName", "registrationNumber", "beginDate", "endDate", "legalClassification", "businessType", description, "orgTypeCode", "orgRoleCodes", "taxIdentifier", "parentOrgId", "ownerOrgId", "isOrgUnit") VALUES ('CONCOM', 'Contrats Commerciaux', NULL, 'Contrats Commerciaux', 'CONCOM', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'SJ', 'ACME', true);
INSERT INTO organization.organization ("orgId", "orgName", "otherOrgName", "displayName", "registrationNumber", "beginDate", "endDate", "legalClassification", "businessType", description, "orgTypeCode", "orgRoleCodes", "taxIdentifier", "parentOrgId", "ownerOrgId", "isOrgUnit") VALUES ('PAIE', 'Rémunération et Paie', NULL, 'Rémunération et Paie', 'PAIE', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'RH', 'ACME', true);
INSERT INTO organization.organization ("orgId", "orgName", "otherOrgName", "displayName", "registrationNumber", "beginDate", "endDate", "legalClassification", "businessType", description, "orgTypeCode", "orgRoleCodes", "taxIdentifier", "parentOrgId", "ownerOrgId", "isOrgUnit") VALUES ('DIP', 'Dossiers du personnel', NULL, 'Dossiers Individuels du Personnel', 'DIP', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'RH', 'ACME', true);
INSERT INTO organization.organization ("orgId", "orgName", "otherOrgName", "displayName", "registrationNumber", "beginDate", "endDate", "legalClassification", "businessType", description, "orgTypeCode", "orgRoleCodes", "taxIdentifier", "parentOrgId", "ownerOrgId", "isOrgUnit") VALUES ('NOTFRA', 'Notes Frais', NULL, 'Gestion des Notes de Frais', 'NOTFRA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'RH', 'ACME', true);
INSERT INTO organization.organization ("orgId", "orgName", "otherOrgName", "displayName", "registrationNumber", "beginDate", "endDate", "legalClassification", "businessType", description, "orgTypeCode", "orgRoleCodes", "taxIdentifier", "parentOrgId", "ownerOrgId", "isOrgUnit") VALUES ('MUT', 'Prévoyance/Mutuelle', NULL, 'Prévoyance/Mutuelle', 'MUT', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'RH', 'ACME', true);
INSERT INTO organization.organization ("orgId", "orgName", "otherOrgName", "displayName", "registrationNumber", "beginDate", "endDate", "legalClassification", "businessType", description, "orgTypeCode", "orgRoleCodes", "taxIdentifier", "parentOrgId", "ownerOrgId", "isOrgUnit") VALUES ('CTBLE', 'Service Comptable', NULL, 'Service Comptable', 'CTBLE', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'DAF', 'ACME', true);
INSERT INTO organization.organization ("orgId", "orgName", "otherOrgName", "displayName", "registrationNumber", "beginDate", "endDate", "legalClassification", "businessType", description, "orgTypeCode", "orgRoleCodes", "taxIdentifier", "parentOrgId", "ownerOrgId", "isOrgUnit") VALUES ('FOUR', 'Achats/Fournisseurs', NULL, 'Comptabilité Achats/Fournisseurs', 'FOUR', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'CTBLE', 'ACME', true);
INSERT INTO organization.organization ("orgId", "orgName", "otherOrgName", "displayName", "registrationNumber", "beginDate", "endDate", "legalClassification", "businessType", description, "orgTypeCode", "orgRoleCodes", "taxIdentifier", "parentOrgId", "ownerOrgId", "isOrgUnit") VALUES ('SALES', 'Ventes/Clients', NULL, 'Comptabilité Ventes/Clients', 'SALES', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'CTBLE', 'ACME', true);
INSERT INTO organization.organization ("orgId", "orgName", "otherOrgName", "displayName", "registrationNumber", "beginDate", "endDate", "legalClassification", "businessType", description, "orgTypeCode", "orgRoleCodes", "taxIdentifier", "parentOrgId", "ownerOrgId", "isOrgUnit") VALUES ('GEN', 'Comptabilité Générale', NULL, 'Comptabilité Générale/Éditions comptables', 'GEN', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'CTBLE', 'ACME', true);
INSERT INTO organization.organization ("orgId", "orgName", "otherOrgName", "displayName", "registrationNumber", "beginDate", "endDate", "legalClassification", "businessType", description, "orgTypeCode", "orgRoleCodes", "taxIdentifier", "parentOrgId", "ownerOrgId", "isOrgUnit") VALUES ('TRESO', 'Trésorerie', NULL, 'Trésorerie', 'TRESO', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'CTBLE', 'ACME', true);
INSERT INTO organization.organization ("orgId", "orgName", "otherOrgName", "displayName", "registrationNumber", "beginDate", "endDate", "legalClassification", "businessType", description, "orgTypeCode", "orgRoleCodes", "taxIdentifier", "parentOrgId", "ownerOrgId", "isOrgUnit") VALUES ('FISCA', 'Fiscalité', NULL, 'Fiscalité', 'FISCA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'CTBLE', 'ACME', true);
INSERT INTO organization.organization ("orgId", "orgName", "otherOrgName", "displayName", "registrationNumber", "beginDate", "endDate", "legalClassification", "businessType", description, "orgTypeCode", "orgRoleCodes", "taxIdentifier", "parentOrgId", "ownerOrgId", "isOrgUnit") VALUES ('ACHAT', 'Achats Groupe', NULL, 'Achats Groupe', 'ACHAT', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'SG', 'ACME', true);
INSERT INTO organization.organization ("orgId", "orgName", "otherOrgName", "displayName", "registrationNumber", "beginDate", "endDate", "legalClassification", "businessType", description, "orgTypeCode", "orgRoleCodes", "taxIdentifier", "parentOrgId", "ownerOrgId", "isOrgUnit") VALUES ('SUPP', 'Support', NULL, 'Support', 'SUPP', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'DSI', 'ACME', true);
INSERT INTO organization.organization ("orgId", "orgName", "otherOrgName", "displayName", "registrationNumber", "beginDate", "endDate", "legalClassification", "businessType", description, "orgTypeCode", "orgRoleCodes", "taxIdentifier", "parentOrgId", "ownerOrgId", "isOrgUnit") VALUES ('DCIAL', 'Direction Commerciale', NULL, 'Direction Commerciale', 'DCIAL', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'ACME', 'ACME', true);
INSERT INTO organization.organization ("orgId", "orgName", "otherOrgName", "displayName", "registrationNumber", "beginDate", "endDate", "legalClassification", "businessType", description, "orgTypeCode", "orgRoleCodes", "taxIdentifier", "parentOrgId", "ownerOrgId", "isOrgUnit") VALUES ('CUST', 'Gestion Clients', NULL, 'Gestion Clients', 'CUST', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'DCIAL', 'ACME', true);
INSERT INTO organization.organization ("orgId", "orgName", "otherOrgName", "displayName", "registrationNumber", "beginDate", "endDate", "legalClassification", "businessType", description, "orgTypeCode", "orgRoleCodes", "taxIdentifier", "parentOrgId", "ownerOrgId", "isOrgUnit") VALUES ('MARK', 'Marketing', NULL, 'Marketing', 'MARK', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'DCIAL', 'ACME', true);
INSERT INTO organization.organization ("orgId", "orgName", "otherOrgName", "displayName", "registrationNumber", "beginDate", "endDate", "legalClassification", "businessType", description, "orgTypeCode", "orgRoleCodes", "taxIdentifier", "parentOrgId", "ownerOrgId", "isOrgUnit") VALUES ('DEAL', 'Offres Commerciales', NULL, 'Offres Commerciales', 'DEAL', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'DCIAL', 'ACME', true);
INSERT INTO organization.organization ("orgId", "orgName", "otherOrgName", "displayName", "registrationNumber", "beginDate", "endDate", "legalClassification", "businessType", description, "orgTypeCode", "orgRoleCodes", "taxIdentifier", "parentOrgId", "ownerOrgId", "isOrgUnit") VALUES ('BILAN', 'Comptes de bilan', NULL, 'Comptes de Bilan et Clôture d''Exercice', 'BILAN', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'CTBLE', 'ACME', true);
INSERT INTO organization.organization ("orgId", "orgName", "otherOrgName", "displayName", "registrationNumber", "beginDate", "endDate", "legalClassification", "businessType", description, "orgTypeCode", "orgRoleCodes", "taxIdentifier", "parentOrgId", "ownerOrgId", "isOrgUnit") VALUES ('AUTO', 'Gestion parc Auto', NULL, 'Gestion du Parc Automobile', 'AUTO', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'SG', 'ACME', true);
INSERT INTO organization.organization ("orgId", "orgName", "otherOrgName", "displayName", "registrationNumber", "beginDate", "endDate", "legalClassification", "businessType", description, "orgTypeCode", "orgRoleCodes", "taxIdentifier", "parentOrgId", "ownerOrgId", "isOrgUnit") VALUES ('NETT', 'Nettoyage des Locaux', NULL, 'Nettoyage des Locaux', 'NETT', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'SG', 'ACME', true);
INSERT INTO organization.organization ("orgId", "orgName", "otherOrgName", "displayName", "registrationNumber", "beginDate", "endDate", "legalClassification", "businessType", description, "orgTypeCode", "orgRoleCodes", "taxIdentifier", "parentOrgId", "ownerOrgId", "isOrgUnit") VALUES ('SOC', 'Charges Sociales', NULL, 'Charges Sociales', 'SOC', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'RH', 'ACME', true);
INSERT INTO organization.organization ("orgId", "orgName", "otherOrgName", "displayName", "registrationNumber", "beginDate", "endDate", "legalClassification", "businessType", description, "orgTypeCode", "orgRoleCodes", "taxIdentifier", "parentOrgId", "ownerOrgId", "isOrgUnit") VALUES ('TENDER', 'Appels d''offres', NULL, 'Réponses aux Appels d''Offres/Collectivités', 'TENDER', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'DCIAL', 'ACME', true);
INSERT INTO organization.organization ("orgId", "orgName", "otherOrgName", "displayName", "registrationNumber", "beginDate", "endDate", "legalClassification", "businessType", description, "orgTypeCode", "orgRoleCodes", "taxIdentifier", "parentOrgId", "ownerOrgId", "isOrgUnit") VALUES ('SIG', 'Gestion systèmes d''informations', NULL, 'Gestion des Systèmes d''Information', 'SIG', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'DSI', 'ACME', true);
INSERT INTO organization.organization ("orgId", "orgName", "otherOrgName", "displayName", "registrationNumber", "beginDate", "endDate", "legalClassification", "businessType", description, "orgTypeCode", "orgRoleCodes", "taxIdentifier", "parentOrgId", "ownerOrgId", "isOrgUnit") VALUES ('SYSRES', 'Système et Réseaux', NULL, 'Système et Réseaux', 'SYSRES', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'DSI', 'ACME', true);

INSERT INTO organization."archivalProfileAccess" ("orgId", "archivalProfileReference", "originatorAccess", "serviceLevelReference", "userAccess") VALUES ('DAF', 'FACACH', false, '', '{"subProfile": {}, "processingStatuses": {"QUALIFIED": {"actions": {"reject": {}, "redirect": {}, "validate": {}}}, "VALIDATED": {"actions": {"reject": {}, "approve": {}}}}}');
INSERT INTO organization."archivalProfileAccess" ("orgId", "archivalProfileReference", "originatorAccess", "serviceLevelReference", "userAccess") VALUES ('DIP', 'DOSIP', true, '', NULL);
INSERT INTO organization."archivalProfileAccess" ("orgId", "archivalProfileReference", "originatorAccess", "serviceLevelReference", "userAccess") VALUES ('DSI', 'FACACH', false, '', '{"subProfile": {}, "processingStatuses": {"QUALIFIED": {"actions": {"reject": {}, "redirect": {}, "validate": {}}}, "VALIDATED": {"actions": {"reject": {}, "approve": {}}}}}');
INSERT INTO organization."archivalProfileAccess" ("orgId", "archivalProfileReference", "originatorAccess", "serviceLevelReference", "userAccess") VALUES ('FOUR', 'FACACH', true, '', '{"history": {}, "subProfile": {}, "processingStatuses": {"NEW": {"actions": {"qualify": {}, "cancelQualify": {}}}, "APPROVED": {"actions": {"pay": {}, "updateMetadata": {}}}, "REJECTED": {"actions": {"cancelQualify": {}, "sendValidation": {}, "updateMetadata": {}, "sendToApprobation": {}}}, "MISQUALIFIED": {"actions": {"qualify": {}}}}}');
INSERT INTO organization."archivalProfileAccess" ("orgId", "archivalProfileReference", "originatorAccess", "serviceLevelReference", "userAccess") VALUES ('FOUR', 'FACJU', true, '', NULL);
INSERT INTO organization."archivalProfileAccess" ("orgId", "archivalProfileReference", "originatorAccess", "serviceLevelReference", "userAccess") VALUES ('SALES', 'FACVEN', true, '', NULL);
INSERT INTO organization."archivalProfileAccess" ("orgId", "archivalProfileReference", "originatorAccess", "serviceLevelReference", "userAccess") VALUES ('SG', 'FACACH', false, '', '{"subProfile": {}, "processingStatuses": {"QUALIFIED": {"actions": {"reject": {}, "redirect": {}, "validate": {}}}, "VALIDATED": {"actions": {"reject": {}, "approve": {}}}}}');
INSERT INTO organization."archivalProfileAccess" ("orgId", "archivalProfileReference", "originatorAccess", "serviceLevelReference", "userAccess") VALUES ('PAIE', 'BULPAI', true, '', '{"subProfile": {}, "processingStatuses": {}}');
INSERT INTO organization."archivalProfileAccess" ("orgId", "archivalProfileReference", "originatorAccess", "serviceLevelReference", "userAccess") VALUES ('DAF', 'NOTSER', true, '', '{"subProfile": {}, "processingStatuses": {}}');
INSERT INTO organization."archivalProfileAccess" ("orgId", "archivalProfileReference", "originatorAccess", "serviceLevelReference", "userAccess") VALUES ('DCIAL', 'NOTSER', true, '', '{"subProfile": {}, "processingStatuses": {}}');
INSERT INTO organization."archivalProfileAccess" ("orgId", "archivalProfileReference", "originatorAccess", "serviceLevelReference", "userAccess") VALUES ('RH', 'NOTSER', true, '', '{"subProfile": {}, "processingStatuses": {}}');
INSERT INTO organization."archivalProfileAccess" ("orgId", "archivalProfileReference", "originatorAccess", "serviceLevelReference", "userAccess") VALUES ('DSI', 'NOTSER', true, '', '{"subProfile": {}, "processingStatuses": {}}');
INSERT INTO organization."archivalProfileAccess" ("orgId", "archivalProfileReference", "originatorAccess", "serviceLevelReference", "userAccess") VALUES ('GIC', 'NOTSER', true, '', '{"subProfile": {}, "processingStatuses": {}}');
INSERT INTO organization."archivalProfileAccess" ("orgId", "archivalProfileReference", "originatorAccess", "serviceLevelReference", "userAccess") VALUES ('TENDER', 'PM', true, '', '{"subProfile": {}, "processingStatuses": {}}');
INSERT INTO organization."archivalProfileAccess" ("orgId", "archivalProfileReference", "originatorAccess", "serviceLevelReference", "userAccess") VALUES ('TENDER', 'COUNM', true, '', '{"subProfile": {}, "processingStatuses": {}}');
INSERT INTO organization."archivalProfileAccess" ("orgId", "archivalProfileReference", "originatorAccess", "serviceLevelReference", "userAccess") VALUES ('DOCSOC', 'FICCR', true, '', '{"subProfile": {}, "processingStatuses": {}}');
INSERT INTO organization."archivalProfileAccess" ("orgId", "archivalProfileReference", "originatorAccess", "serviceLevelReference", "userAccess") VALUES ('DOCSOC', 'LETC', true, '', '{"subProfile": {}, "processingStatuses": {}}');
INSERT INTO organization."archivalProfileAccess" ("orgId", "archivalProfileReference", "originatorAccess", "serviceLevelReference", "userAccess") VALUES ('DOCSOC', 'FICI', true, '', '{"subProfile": {}, "processingStatuses": {}}');
INSERT INTO organization."archivalProfileAccess" ("orgId", "archivalProfileReference", "originatorAccess", "serviceLevelReference", "userAccess") VALUES ('MARK', 'FACACH', false, '', '{"subProfile": {}, "processingStatuses": {"QUALIFIED": {"actions": {"reject": {}, "redirect": {}, "validate": {}}}}}');
INSERT INTO organization."archivalProfileAccess" ("orgId", "archivalProfileReference", "originatorAccess", "serviceLevelReference", "userAccess") VALUES ('DCIAL', 'FACACH', false, '', '{"subProfile": {}, "processingStatuses": {"VALIDATED": {"actions": {"reject": {}, "approve": {}}}}}');

INSERT INTO organization."servicePosition" ("serviceAccountId", "orgId") VALUES ('SystemDepositor', 'GIC');
INSERT INTO organization."servicePosition" ("serviceAccountId", "orgId") VALUES ('System', 'GIC');

INSERT INTO organization."userPosition" ("userAccountId", "orgId", function, "default") VALUES ('ddaull', 'DAF', NULL, true);
INSERT INTO organization."userPosition" ("userAccountId", "orgId", function, "default") VALUES ('jjonasz', 'LITIGES', NULL, true);
INSERT INTO organization."userPosition" ("userAccountId", "orgId", function, "default") VALUES ('aackermann', 'FOUR', NULL, true);
INSERT INTO organization."userPosition" ("userAccountId", "orgId", function, "default") VALUES ('aalambic', 'FOUR', NULL, true);
INSERT INTO organization."userPosition" ("userAccountId", "orgId", function, "default") VALUES ('ccordy', 'FOUR', NULL, true);
INSERT INTO organization."userPosition" ("userAccountId", "orgId", function, "default") VALUES ('ccamus', 'TRESO', NULL, true);
INSERT INTO organization."userPosition" ("userAccountId", "orgId", function, "default") VALUES ('bboule', 'SALES', NULL, true);
INSERT INTO organization."userPosition" ("userAccountId", "orgId", function, "default") VALUES ('ddur', 'SALES', NULL, true);
INSERT INTO organization."userPosition" ("userAccountId", "orgId", function, "default") VALUES ('eerina', 'SALES', NULL, true);
INSERT INTO organization."userPosition" ("userAccountId", "orgId", function, "default") VALUES ('ttong', 'SALES', NULL, true);
INSERT INTO organization."userPosition" ("userAccountId", "orgId", function, "default") VALUES ('bbain', 'SJ', NULL, true);
INSERT INTO organization."userPosition" ("userAccountId", "orgId", function, "default") VALUES ('rrenaud', 'SG', NULL, true);
INSERT INTO organization."userPosition" ("userAccountId", "orgId", function, "default") VALUES ('ssaporta', 'AUTO', NULL, true);
INSERT INTO organization."userPosition" ("userAccountId", "orgId", function, "default") VALUES ('ppacioli', 'NETT', NULL, true);
INSERT INTO organization."userPosition" ("userAccountId", "orgId", function, "default") VALUES ('kkaar', 'TENDER', NULL, true);
INSERT INTO organization."userPosition" ("userAccountId", "orgId", function, "default") VALUES ('ssissoko', 'CUST', NULL, true);
INSERT INTO organization."userPosition" ("userAccountId", "orgId", function, "default") VALUES ('kkrach', 'MARK', NULL, true);
INSERT INTO organization."userPosition" ("userAccountId", "orgId", function, "default") VALUES ('mmanfred', 'DEAL', NULL, true);
INSERT INTO organization."userPosition" ("userAccountId", "orgId", function, "default") VALUES ('vvictoire', 'RH', NULL, true);
INSERT INTO organization."userPosition" ("userAccountId", "orgId", function, "default") VALUES ('hhier', 'PAIE', NULL, true);
INSERT INTO organization."userPosition" ("userAccountId", "orgId", function, "default") VALUES ('ppreboist', 'DSI', NULL, true);
INSERT INTO organization."userPosition" ("userAccountId", "orgId", function, "default") VALUES ('ppruvost', 'DSI', NULL, true);
INSERT INTO organization."userPosition" ("userAccountId", "orgId", function, "default") VALUES ('sstallone', 'SUPP', NULL, true);
INSERT INTO organization."userPosition" ("userAccountId", "orgId", function, "default") VALUES ('sstar', 'SUPP', NULL, true);
INSERT INTO organization."userPosition" ("userAccountId", "orgId", function, "default") VALUES ('ttule', 'SYSRES', NULL, true);
INSERT INTO organization."userPosition" ("userAccountId", "orgId", function, "default") VALUES ('ddenis', 'GIC', NULL, true);
INSERT INTO organization."userPosition" ("userAccountId", "orgId", function, "default") VALUES ('ccharles', 'GIC', NULL, true);
INSERT INTO organization."userPosition" ("userAccountId", "orgId", function, "default") VALUES ('aadams', 'DAF', NULL, true);
INSERT INTO organization."userPosition" ("userAccountId", "orgId", function, "default") VALUES ('aastier', 'SG', NULL, true);
INSERT INTO organization."userPosition" ("userAccountId", "orgId", function, "default") VALUES ('workflow_pod5l0-232e-0aggqt', 'GEN', NULL, true);
INSERT INTO organization."userPosition" ("userAccountId", "orgId", function, "default") VALUES ('bblier', 'GIC', NULL, true);
INSERT INTO organization."userPosition" ("userAccountId", "orgId", function, "default") VALUES ('cchaplin', 'RH', NULL, true);
INSERT INTO organization."userPosition" ("userAccountId", "orgId", function, "default") VALUES ('ccox', 'SJ', NULL, true);
INSERT INTO organization."userPosition" ("userAccountId", "orgId", function, "default") VALUES ('ggrand', 'RH', NULL, true);
INSERT INTO organization."userPosition" ("userAccountId", "orgId", function, "default") VALUES ('jjane', 'DOCSOC', NULL, true);
INSERT INTO organization."userPosition" ("userAccountId", "orgId", function, "default") VALUES ('nnataly', 'DSI', NULL, true);
INSERT INTO organization."userPosition" ("userAccountId", "orgId", function, "default") VALUES ('ppetit', 'CTBLE', NULL, true);
INSERT INTO organization."userPosition" ("userAccountId", "orgId", function, "default") VALUES ('rreynolds', 'ACHAT', NULL, true);
INSERT INTO organization."userPosition" ("userAccountId", "orgId", function, "default") VALUES ('sstone', 'DCIAL', NULL, true);
